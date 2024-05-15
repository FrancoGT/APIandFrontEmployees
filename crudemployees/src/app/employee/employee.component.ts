import { Component, OnInit } from '@angular/core';
import { EmployeeApiService } from '../employee-api.service';

@Component({
  selector: 'app-employee',
  templateUrl: './employee.component.html',
  styleUrls: ['./employee.component.css']
})
export class EmployeeComponent implements OnInit 
{
  employees: any[] = [];
  isModalVisible = false;
  isEditModalVisible = false;
  newEmployeeData: any = {};
  editEmployeeData: any = {};
  pagedEmployees: any[] = [];
  currentPage = 1;
  errorMessage: string | null = null;
  successMessage: string | null = null;

  constructor(private employeeService: EmployeeApiService) {}

  ngOnInit(): void 
  {
    this.loadEmployees();
  }

  loadEmployees(): void 
  {
    this.employeeService.getEmployees().subscribe(data => {
      this.employees = data;
      this.setPage(1);
    });
  }

  setPage(page: number): void 
  {
    this.currentPage = page;
    this.pagedEmployees = this.employees.slice((page - 1) * 10, page * 10);
  }

  pageChanged(page: number): void 
  {
    this.setPage(page);
  }

  openModal(): void 
  {
    this.isModalVisible = true;
  }

  closeModal(): void 
  {
    this.isModalVisible = false;
    this.newEmployeeData = {};
    this.errorMessage = null;
    this.successMessage = null;
  }

  openEditModal(employee: any): void 
  {
    this.isEditModalVisible = true;
    this.editEmployeeData = { ...employee };
  }

  closeEditModal(): void 
  {
    this.isEditModalVisible = false;
    this.editEmployeeData = {};
    this.errorMessage = null;
    this.successMessage = null;
  }

  saveEmployee(): void 
  {
    if (!this.validateEmployee(this.newEmployeeData)) 
    {
      return;
    }

    this.employeeService.createEmployee(this.newEmployeeData).subscribe(
      (response) => {
        this.loadEmployees();
        this.closeModal();
        this.showSuccessMessage('Empleado guardado correctamente.');
      },
      (error) => {
        console.error('Error al guardar el empleado:', error);
        this.errorMessage = 'Error al guardar el empleado.';
      }
    );
  }

  updateEmployee(): void 
  {
    if (!this.validateEmployee(this.editEmployeeData)) 
    {
      return;
    }

    this.employeeService.updateEmployee(this.editEmployeeData.id, this.editEmployeeData).subscribe(
      response => {
        this.loadEmployees();
        this.closeEditModal();
        this.showSuccessMessage('Empleado actualizado correctamente.');
      },
      error => {
        console.error('Error al actualizar el empleado:', error);
        this.errorMessage = 'Error al actualizar el empleado.';
      }
    );
  }

  validateEmployee(employee: any): boolean 
  {
    const requiredFields = ['employment_country', 'first_name', 'hire_date', 'identification_number', 'identification_type', 'last_name', 'area'];
    for (const field of requiredFields) {
      if (!employee[field]) {
        this.errorMessage = `El campo ${field} es obligatorio.`;
        return false;
      }
    }
    this.errorMessage = null;
    return true;
  }

  confirmDelete(id: number): void 
  {
    if (confirm('¿Estás seguro de que deseas eliminar este empleado?')) {
      this.deleteEmployee(id);
    }
  }

  deleteEmployee(id: number): void 
  {
    this.employeeService.deleteEmployee(id).subscribe(
      response => {
        this.loadEmployees();
        this.showSuccessMessage('Empleado eliminado correctamente.');
      },
      error => {
        console.error('Error al eliminar el empleado:', error);
      }
    );
  }

  showSuccessMessage(message: string): void 
  {
    this.successMessage = message;
    setTimeout(() => {
      this.successMessage = null;
    }, 3000);
  }
}