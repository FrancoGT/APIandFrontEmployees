import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, catchError, throwError } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class EmployeeApiService 
{
  private apiUrl = 'http://localhost:8000/api/employees';

  constructor(private http: HttpClient) {}

  getEmployees(): Observable<any[]> 
  {
    return this.http.get<any[]>(this.apiUrl);
  }

  getEmployeeById(id: number): Observable<any> 
  {
    const url = `${this.apiUrl}/${id}`;
    return this.http.get<any>(url).pipe(
      catchError(this.handleError)
    );
  }

  createEmployee(employeeData: any): Observable<any> 
  {
    // Encabezados para la solicitud POST
    const httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json'
      })
    };
    return this.http.post<any>(this.apiUrl, employeeData, httpOptions).pipe(
      catchError(this.handleError)
    );
  }

  updateEmployee(id: number, employeeData: any): Observable<any> 
  {
    const url = `${this.apiUrl}/${id}`;
    // Encabezados para la solicitud PUT
    const httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json'
      })
    };
    return this.http.put<any>(url, employeeData, httpOptions).pipe(
      catchError(this.handleError)
    );
  }

  deleteEmployee(id: number): Observable<any> 
  {
    const url = `${this.apiUrl}/${id}`;
    return this.http.delete<any>(url).pipe(
      catchError(this.handleError)
    );
  }

  // Método para manejar errores de la solicitud HTTP
  private handleError(error: any): Observable<never> 
  {
    let errorMessage = 'Error: ';
    if (error.error instanceof ErrorEvent) 
    {
      // Error del cliente, como una red rota
      errorMessage = `Error: ${error.error.message}`;
    } 
    else 
    {
      // El servidor devolvió un código de respuesta no exitoso
      errorMessage = `Código de error: ${error.status}, Mensaje: ${error.message}`;
    }
    console.error(errorMessage);
    return throwError(errorMessage); // Devolver un observable con el error
  }
}