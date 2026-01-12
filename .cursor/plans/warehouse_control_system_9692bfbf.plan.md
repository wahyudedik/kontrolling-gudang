---
name: ""
overview: ""
todos:
  - id: todo-1768252893497-g8egap154
    content: ""
    status: pending
---

---name: Warehouse Control Systemoverview: Membangun aplikasi web berbasis Laravel untuk kontroling gudang dengan sistem role-based (Super Admin & Supervisor), To Do List harian, input progress report, dan export Excel dengan filter rentang tanggal, task, dan supervisor.todos:

- id: install-breeze

content: Install Laravel Breeze dengan stack Blade untuk autentikasistatus: pending

- id: database-schema

content: "Buat migrations untuk semua tabel dengan UUID primary key: users (ubah id ke UUID, add role), todo_lists, todo_items, daily_reports, dan 5 tabel report detail. Report warehouse condition: struktur checklist (warehouse enum, check_1 sampai check_5 boolean)"status: pending

- id: models-relationships

content: Buat semua Eloquent models dengan UUID primary key dan relationships yang tepat, setup eager loading di model scopesstatus: pendingdependencies:

    - database-schema
- id: role-middleware

content: Buat middleware untuk Super Admin dan Supervisor, update User model dengan role methodsstatus: pendingdependencies:

    - database-schema
- id: form-requests

content: "Buat Form Request classes untuk validasi: StoreTodoListRequest, UpdateTodoListRequest, StoreDailyReportRequest, UpdateDailyReportRequest, FilterReportRequest, FilterTodoRequest"status: pendingdependencies:

    - models-relationships
- id: todo-list-crud

content: Implementasi CRUD To Do List untuk Super Admin dengan Form Request validation, eager loading untuk menghindari N+1status: pendingdependencies:

    - models-relationships
    - role-middleware
    - form-requests
- id: supervisor-todos-view

content: Buat halaman supervisor untuk menampilkan todos dalam card layout dengan filter lengkap (due date, task, nama SPV), dengan button Isi Report pada setiap cardstatus: pendingdependencies:

    - models-relationships
    - role-middleware
- id: daily-report-form

content: Buat form input daily report untuk Supervisor dengan 5 section (Kondisi Gudang: 6 gudang x 5 checkbox), frontend validation (HTML5 + JS), backend validation via Form Request, eager loading untuk viewstatus: pendingdependencies:

    - models-relationships
    - role-middleware
    - form-requests
    - supervisor-todos-view
- id: report-viewing

content: Implementasi halaman view reports untuk Super Admin dengan filter lengkap (due date, task, nama SPV, date range), Form Request validation, eager loading semua relationships untuk menghindari N+1 queriesstatus: pendingdependencies:

    - models-relationships
    - role-middleware
    - form-requests
- id: excel-export

content: Install maatwebsite/excel dan buat service untuk export reports ke Excel berdasarkan filter aktifstatus: pendingdependencies:

    - report-viewing
- id: routes-middleware

content: Setup routes dengan middleware protection dan grouping berdasarkan rolestatus: pendingdependencies:

    - todo-list-crud
    - daily-report-form
    - report-viewing
- id: ui-polish

content: Styling dengan Tailwind CSS, tambah date picker, frontend validation (HTML5 + JavaScript), validation feedback, dan notificationsstatus: pendingdependencies:

    - todo-list-crud
    - daily-report-form
    - report-viewing

---

# Rencana Implementasi Aplikasi Kontroling Gudang

## Arsitektur Sistem

Aplikasi web berbasis Laravel 12 dengan autentikasi Laravel Breeze, menggunakan Tailwind CSS untuk UI. Sistem akan memiliki 2 role utama: Super Admin dan Supervisor dengan hak akses berbeda.

## Database Schema

**Catatan Penting**: Semua tabel menggunakan **UUID** sebagai primary key (bukan auto-increment ID) untuk keamanan dan skalabilitas.

### Tabel Utama

1. **users** (extend existing)

- `id` (UUID, primary key)
- Tambah kolom `role` (enum: 'super_admin', 'supervisor')

2. **todo_lists**

- `id` (UUID, primary key)
- `title` (string)
- `type` (enum: 'template', 'daily')
- `date` (date, nullable, untuk daily)
- `created_by` (UUID, foreign key ke users)
- `is_active` (boolean)
- `timestamps`

3. **todo_items**

- `id` (UUID, primary key)
- `todo_list_id` (UUID, foreign key ke todo_lists)
- `item_type` (enum: 'man_power', 'stock_finish_good', 'stock_raw_material', 'warehouse_condition', 'supplier')
- `order` (integer)
- `timestamps`

4. **daily_reports**

- `id` (UUID, primary key)
- `todo_list_id` (UUID, foreign key ke todo_lists)
- `supervisor_id` (UUID, foreign key ke users)
- `report_date` (date)
- `status` (enum: 'draft', 'completed')
- `timestamps`

5. **report_man_power**

- `id` (UUID, primary key)
- `daily_report_id` (UUID, foreign key ke daily_reports)
- `employees_present` (integer)
- `employees_absent` (integer)
- `timestamps`

6. **report_stock_finish_good**

- `id` (UUID, primary key)
- `daily_report_id` (UUID, foreign key ke daily_reports)
- `item_name` (string)
- `quantity` (integer, satuan: karton)
- `timestamps`

7. **report_stock_raw_material**

- `id` (UUID, primary key)
- `daily_report_id` (UUID, foreign key ke daily_reports)
- `item_name` (string)
- `quantity` (decimal, satuan: kg)
- `timestamps`

8. **report_warehouse_condition**

- `id` (UUID, primary key)
- `daily_report_id` (UUID, foreign key ke daily_reports)
- `warehouse` (enum: 'cs1', 'cs2', 'cs3', 'cs4', 'cs5', 'cs6')
- `check_1` (boolean, default false) - contoh: "Bersih bersih banget"
- `check_2` (boolean, default false)
- `check_3` (boolean, default false)
- `check_4` (boolean, default false)
- `check_5` (boolean, default false)
- `notes` (text, nullable)
- `timestamps`

**Catatan**: Setiap baris mewakili satu gudang (CS1-CS6) dengan 5 checkbox. Bisa ada multiple rows untuk satu daily_report (satu row per gudang yang dicek).

9. **report_suppliers**

- `id` (UUID, primary key)
- `daily_report_id` (UUID, foreign key ke daily_reports)
- `supplier_name` (string)
- `timestamps`

## Struktur File yang Akan Dibuat

### Migrations

- `database/migrations/xxxx_modify_users_table_add_uuid_and_role.php` (ubah id ke UUID, tambah role)
- `database/migrations/xxxx_create_todo_lists_table.php` (UUID primary key)
- `database/migrations/xxxx_create_todo_items_table.php` (UUID primary key)
- `database/migrations/xxxx_create_daily_reports_table.php` (UUID primary key)
- `database/migrations/xxxx_create_report_man_power_table.php` (UUID primary key)
- `database/migrations/xxxx_create_report_stock_finish_good_table.php` (UUID primary key)
- `database/migrations/xxxx_create_report_stock_raw_material_table.php` (UUID primary key)
- `database/migrations/xxxx_create_report_warehouse_condition_table.php` (UUID primary key)
- `database/migrations/xxxx_create_report_suppliers_table.php` (UUID primary key)

### Models

- `app/Models/TodoList.php` (UUID, relationships dengan eager loading)
- `app/Models/TodoItem.php` (UUID, relationships)
- `app/Models/DailyReport.php` (UUID, relationships dengan eager loading untuk menghindari N+1)
- `app/Models/ReportManPower.php` (UUID, relationships)
- `app/Models/ReportStockFinishGood.php` (UUID, relationships)
- `app/Models/ReportStockRawMaterial.php` (UUID, relationships)
- `app/Models/ReportWarehouseCondition.php` (UUID, relationships)
- `app/Models/ReportSupplier.php` (UUID, relationships)

**Catatan**: Semua models menggunakan UUID sebagai primary key dan implement eager loading di relationships untuk menghindari N+1 queries.

### Controllers

- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (via Breeze)
- `app/Http/Controllers/TodoListController.php` (Super Admin: CRUD To Do List, gunakan eager loading)
- `app/Http/Controllers/SupervisorTodoController.php` (Supervisor: view todos dalam card layout)
- `app/Http/Controllers/DailyReportController.php` (Supervisor: input progress, gunakan eager loading)
- `app/Http/Controllers/ReportController.php` (Super Admin: view & export reports, gunakan eager loading untuk menghindari N+1)
- `app/Http/Controllers/ProfileController.php` (via Breeze)

### Form Requests (Validation)

- `app/Http/Requests/StoreTodoListRequest.php` (validasi create To Do List)
- `app/Http/Requests/UpdateTodoListRequest.php` (validasi update To Do List)
- `app/Http/Requests/StoreDailyReportRequest.php` (validasi create daily report dengan semua section)
- `app/Http/Requests/UpdateDailyReportRequest.php` (validasi update daily report)
- `app/Http/Requests/FilterReportRequest.php` (validasi filter report: due date, task, nama SPV, date range)
- `app/Http/Requests/FilterTodoRequest.php` (validasi filter todos: due date, task, nama SPV)

### Middleware

- `app/Http/Middleware/EnsureUserIsSuperAdmin.php`
- `app/Http/Middleware/EnsureUserIsSupervisor.php`

### Views (Blade + Tailwind)

- `resources/views/layouts/app.blade.php` (main layout)
- `resources/views/dashboard.blade.php` (role-based dashboard)
- `resources/views/todo-lists/index.blade.php` (Super Admin: list To Do Lists)
- `resources/views/todo-lists/create.blade.php` (Super Admin: create To Do List)
- `resources/views/todo-lists/edit.blade.php` (Super Admin: edit To Do List)
- `resources/views/supervisor/todos.blade.php` (Supervisor: list todos dalam card-card)
- `resources/views/daily-reports/create.blade.php` (Supervisor: input form untuk fill todo)
- `resources/views/daily-reports/show.blade.php` (view report)
- `resources/views/reports/index.blade.php` (Super Admin: filter & list reports)
- `resources/views/auth/*` (via Breeze)

### Routes

- Update `routes/web.php` dengan routes untuk semua fitur
- Group routes dengan middleware role-based

### Services

- `app/Services/ExcelExportService.php` (handle export Excel menggunakan Maatwebsite Excel)

## Fitur Implementasi

### 1. Autentikasi & Authorization

- Install Laravel Breeze dengan stack Blade
- Ubah users table: `id` menjadi UUID, tambah kolom `role` (enum: 'super_admin', 'supervisor')
- Buat middleware untuk role checking
- Update User model dengan method `isSuperAdmin()` dan `isSupervisor()`
- Update User model untuk menggunakan UUID sebagai primary key

### 2. Super Admin Features

#### To Do List Management

- **Create To Do List**: Form dengan pilihan type (template/daily)
- Frontend validation (HTML5 + JavaScript)
- Backend validation via `StoreTodoListRequest`
- Jika template: tidak perlu tanggal
- Jika daily: wajib pilih tanggal
- **List To Do Lists**: Tampilkan semua To Do Lists dengan filter type
- Gunakan eager loading untuk menghindari N+1 queries
- **Edit/Delete To Do List**: Update dan hapus To Do List
- Backend validation via `UpdateTodoListRequest`

#### Report Management

- **View Reports**: Halaman dengan filter lengkap:
- **Due Date**: Filter berdasarkan tanggal due date (date picker)
- **Task/To Do List**: Filter berdasarkan task/To Do List tertentu (dropdown/select)
- **Nama SPV**: Filter berdasarkan nama Supervisor (dropdown/select dengan search)
- **Date Range**: Filter berdasarkan rentang tanggal report (From Date - To Date)
- Backend validation via `FilterReportRequest` untuk semua filter
- **Gunakan eager loading** untuk semua relationships (supervisor, todo_list, dan semua report detail tables) untuk menghindari N+1 queries
- Filter dapat dikombinasikan (multiple filters aktif bersamaan)
- **Export Excel**: Export hasil filter ke Excel dengan format rapi
- Gunakan data yang sudah di-eager load untuk performa optimal

### 3. Supervisor Features

#### To Do Lists View (Card Layout)

- **Menu Supervisor**: Hanya menampilkan halaman todos
- **Card Layout**: Tampilkan semua To Do Lists dalam bentuk card-card
- Setiap card menampilkan: title, type (template/daily), date (jika daily), status, created by (nama SPV)
- Button "Isi Report" pada setiap card untuk mulai input
- **Filter lengkap**:
    - **Due Date**: Filter berdasarkan tanggal due date/tanggal todo (date picker)
    - **Task**: Filter berdasarkan task/To Do List tertentu (dropdown/select dengan search)
    - **Nama SPV**: Filter berdasarkan nama Supervisor yang membuat todo (dropdown/select dengan search)
- Filter dapat dikombinasikan (multiple filters aktif bersamaan)
- Sort: by date (newest/oldest), by type, by status
- Backend validation via request untuk filter
- Gunakan eager loading untuk menghindari N+1 queries (load createdBy relationship)

#### Daily Report Input

- **Access via Card**: Klik "Isi Report" pada card todo untuk mulai input
- **Input Form** dengan 5 section:

1. **Man Power**: Input jumlah karyawan masuk & tidak masuk

- Frontend validation (required, numeric, min 0)

2. **Stock Finish Good**: Add More items (nama item, quantity dalam karton)

- Frontend validation (item_name required, quantity required & numeric)
- Bisa tambah multiple items dengan button "Add More"

3. **Stock Raw Material**: Add More items (nama item, quantity dalam kg)

- Frontend validation (item_name required, quantity required & numeric/decimal)
- Bisa tambah multiple items dengan button "Add More"

4. **Kondisi Gudang**: Checklist untuk 6 gudang (CS1-CS6)

- Setiap gudang memiliki 5 checkbox:
    - Checkbox 1: "Bersih bersih banget" (contoh)
    - Checkbox 2: (label sesuai kebutuhan)
    - Checkbox 3: (label sesuai kebutuhan)
    - Checkbox 4: (label sesuai kebutuhan)
    - Checkbox 5: (label sesuai kebutuhan)
- Layout: 6 kolom (satu kolom per gudang CS1-CS6)
- Setiap kolom berisi 5 checkbox vertikal
- Frontend validation: minimal satu checkbox harus dicentang
- Bisa centang multiple checkbox per gudang

5. **Supplier Datang**: Add More list supplier (nama supplier)

- Frontend validation (supplier_name required)
- Bisa tambah multiple suppliers dengan button "Add More"
- **Save Report**: Simpan progress harian
- Backend validation via `StoreDailyReportRequest` dengan validasi untuk semua section
- Validasi array untuk multi-item inputs (stock finish good, raw material, suppliers)
- Validasi array untuk warehouse conditions (6 gudang x 5 checkbox)
- **View My Reports**: Lihat report yang sudah dibuat
- Gunakan eager loading untuk menghindari N+1 queries saat menampilkan report details

### 4. Excel Export

- Install package `maatwebsite/excel`
- Buat service untuk generate Excel dengan format:
- Header: Date Range, Filters Applied
- Data: Grouped by Supervisor & Date
- Columns: All input fields dari daily reports

## Alur Data

```javascript
Super Admin
  ├─ Create To Do List (Template/Daily)
  └─ View Reports → Filter → Export Excel

Supervisor
  ├─ View Todos (Card Layout)
  ├─ Click Card → Fill Daily Report (5 sections)
  │   ├─ Man Power
  │   ├─ Stock Finish Good (Add More)
  │   ├─ Stock Raw Material (Add More)
  │   ├─ Kondisi Gudang (6 gudang x 5 checkbox)
  │   └─ Supplier Datang (Add More)
  └─ Save Report

System
  └─ Store: daily_reports + related tables (per tanggal, per supervisor)
```



## Dependencies Tambahan

- `maatwebsite/excel` - untuk export Excel
- Laravel Breeze (via composer)

## UI/UX Considerations

- Date picker menggunakan HTML5 date input atau library seperti Flatpickr
- **Frontend Validation**:
- HTML5 validation attributes (required, type, min, max)
- JavaScript validation untuk complex rules
- Real-time validation feedback
- Display validation errors dari backend
- Form dengan validation feedback (error messages, success states)
- Responsive design dengan Tailwind CSS
- Loading states untuk export Excel
- Success/error notifications

## Security

- Role-based route protection
- Mass assignment protection di models (gunakan `$fillable` atau `$guarded`)
- CSRF protection (default Laravel)
- Input validation & sanitization via Form Request classes
- UUID sebagai primary key untuk mencegah ID enumeration attacks

## Performance Optimization

### N+1 Query Prevention

- **Gunakan eager loading** di semua controller methods:
- `TodoListController@index`: `TodoList::with('createdBy', 'items')->get()`
- `SupervisorTodoController@index`: `TodoList::with('createdBy')->where('is_active', true)->get()`
- `DailyReportController@index`: `DailyReport::with('supervisor', 'todoList', 'manPower', 'stockFinishGood', 'stockRawMaterial', 'warehouseConditions', 'suppliers')->get()`
- `ReportController@index`: Eager load semua relationships yang diperlukan