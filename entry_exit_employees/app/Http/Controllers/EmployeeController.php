<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    private function generateUniqueEmail($firstName, $lastName, $employmentCountry, $currentEmail = null)
    {
        $emailDomain = ($employmentCountry == 'Colombia' ? 'co' : 'us');
        $baseEmail = strtolower("{$firstName}.{$lastName}@global.com.{$emailDomain}");

        // If the base email matches the current email, it's already unique
        if ($currentEmail && $currentEmail == $baseEmail) {
            return $currentEmail;
        }

        $email = $baseEmail;
        $counter = 0;

        // Check if the email already exists in the database
        while (Employee::where('email', $email)->exists()) 
        {
            $counter++;
            $email = strtolower("{$firstName}.{$lastName}.{$counter}@global.com.{$emailDomain}");
        }

        // Ensure email length does not exceed 300 characters
        if (strlen($email) > 300) 
        {
            $email = substr($email, 0, 300);
        }

        return $email;
    }

    public function store(Request $request)
    {
        try 
        {
            // Validar los datos de entrada
            $request->validate([
                'first_name' => 'required|alpha|max:20',
                'last_name' => 'required|alpha|max:20',
                'middle_names' => 'nullable|alpha_spaces|max:50',
                'employment_country' => 'required|in:Colombia,Estados Unidos',
                'identification_type' => 'required',
                'identification_number' => 'required|unique:employees|max:20',
                'hire_date' => 'required|date|before_or_equal:today|after_or_equal:' . now()->subMonth(),
                'area' => 'required|in:Administración,Financiera,Compras,Infraestructura,Operación,Talento Humano,Servicios Varios'
            ]);

            // Crear el empleado
            $employee = new Employee();
            $employee->first_name = $request->first_name;
            $employee->last_name = $request->last_name;
            $employee->middle_names = $request->middle_names;
            $employee->employment_country = $request->employment_country;
            $employee->identification_type = $request->identification_type;
            $employee->identification_number = $request->identification_number;

            // Generar el correo electrónico
            $employee->email = $this->generateUniqueEmail($request->first_name, $request->last_name, $request->employment_country);

            $employee->hire_date = $request->hire_date;
            $employee->area = $request->area;
            $employee->status = 'Activo';

            // Guardar el empleado
            $employee->save();

            // Retornar mensaje de éxito
            return response()->json(['message' => 'Empleado creado correctamente'], 201);
        } 
        catch (ValidationException $e) 
        {
            // Capturar los errores de validación y devolverlos como respuesta JSON
            return response()->json(['errors' => $e->errors()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try 
        {
            // Validar los datos de entrada
            $request->validate([
                'first_name' => 'required|alpha|max:20',
                'last_name' => 'required|alpha|max:20',
                'middle_names' => 'nullable|alpha_spaces|max:50',
                'employment_country' => 'required|in:Colombia,Estados Unidos',
                'identification_type' => 'required',
                'identification_number' => 'required|max:20|unique:employees,identification_number,' . $id,
                'hire_date' => 'required|date|before_or_equal:today|after_or_equal:' . now()->subMonth(),
                'area' => 'required|in:Administración,Financiera,Compras,Infraestructura,Operación,Talento Humano,Servicios Varios'
            ]);

            // Buscar al empleado por ID
            $employee = Employee::findOrFail($id);

            // Actualizar los campos del empleado
            $employee->first_name = $request->first_name;
            $employee->last_name = $request->last_name;
            $employee->middle_names = $request->middle_names;
            $employee->employment_country = $request->employment_country;
            $employee->identification_type = $request->identification_type;
            $employee->identification_number = $request->identification_number;

            // Verificar si los nombres y/o apellidos han cambiado para regenerar el correo electrónico
            if ($employee->isDirty(['first_name', 'last_name', 'employment_country'])) 
            {
                $employee->email = $this->generateUniqueEmail($request->first_name, $request->last_name, $request->employment_country, $employee->email);
            }

            $employee->hire_date = $request->hire_date;
            $employee->area = $request->area;
            $employee->status = 'Activo';
            $employee->updated_at = now(); // Fecha de edición

            // Guardar los cambios del empleado
            $employee->save();

            // Retornar mensaje de éxito
            return response()->json(['message' => 'Empleado actualizado correctamente'], 200);
        } 
        catch (ModelNotFoundException $e) 
        {
            // Capturar el error si el empleado no se encuentra
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        } catch (ValidationException $e) 
        {
            // Capturar los errores de validación y devolverlos como respuesta JSON
            return response()->json(['errors' => $e->errors()], 400);
        } 
        catch (\Exception $e) 
        {
            // Capturar cualquier otro error y devolver una respuesta genérica
            return response()->json(['message' => 'Ocurrió un error al actualizar el empleado'], 500);
        }
    }


    public function show($id)
    {
        try {
            // Buscar al empleado por ID
            $employee = Employee::findOrFail($id);

            // Retornar la información del empleado
            return response()->json($employee, 200);
        } catch (ModelNotFoundException $e) {
            // Si no se encuentra el empleado, devolver un error 404
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        } catch (\Exception $e) {
            // Capturar cualquier otro error y devolver una respuesta genérica
            return response()->json(['message' => 'Ocurrió un error al recuperar la información del empleado'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Buscar al empleado por ID
            $employee = Employee::findOrFail($id);

            // Eliminar al empleado
            $employee->delete();

            // Retornar mensaje de éxito
            return response()->json(['message' => 'Empleado eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            // Capturar el error si el empleado no se encuentra
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        } catch (\Exception $e) {
            // Capturar cualquier otro error y devolver una respuesta genérica
            return response()->json(['message' => 'Ocurrió un error al eliminar el empleado'], 500);
        }
    }
}
