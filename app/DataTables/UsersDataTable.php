<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\{Builder as HtmlBuilder, Button, Column};
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($user) {
                return $user->created_at->format('d M Y');
            })
            ->rawColumns(['action']);
    }

    public function query(User $model)
    {
        return $model->newQuery()->select('id', 'name', 'email', 'created_at');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reload')
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')->title('Serials')->searchable(false)->orderable(false),
            Column::make('name'),
            Column::make('email'),
            Column::make('created_at')->title('Registered'),
           
        ];
    }
}
