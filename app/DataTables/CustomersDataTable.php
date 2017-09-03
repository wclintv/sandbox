<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\CustomerSearchData;
use App\Models\Office;
use Yajra\Datatables\Services\DataTable;
use Auth;

class CustomersDataTable extends DataTable
{
    public function dataTable()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->editColumn('firstname', function($customersearchdata){
                return '<a href="/customers/' . $customersearchdata->cust_id . '">' . $customersearchdata->firstname . '</a>';
            })
            ->editColumn('lastname', function($customersearchdata){
                return '<a href="/customers/' . $customersearchdata->cust_id . '">' . $customersearchdata->lastname . '</a>';
            })
            ->rawColumns(['firstname','lastname']);
    }
    public function query()
    {

        //$office_id = Office::office_id_get(Auth::user()->qb_realmid);
        //$query = CustomerSearchData::where('office_id','=',$office_id);
        $query = CustomerSearchData::where('office_id','=', Auth::user()->qbo_membership->office->office_id);
        if($query != null)
        {
           return $this->applyScopes($query); 
        }
        return null;
    }
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->ajax('')

                    ->parameters($this->getBuilderParameters());
    }
    protected function getColumns()
    {
        return [
            'firstname' => ['title' => 'First Name'],
            'lastname'  => ['title' => 'Last Name'],
            'phone'     => ['title' => 'Phone'],
            'mobilephone'  => ['title' => 'Mobile'],
            'address1'  => ['title' => 'Address'],
        ];
    }
    protected function filename()
    {
        return 'customersdatatable_' . time();
    }
    protected function getBuilderParameters()
    {
        $result = [
            'dom' => 'Bfrtip',
            'buttons' => [],
            'processing' => false,
            'serverSide' => true,
            'filter' => false,
            'lengthChange' => false,
            'paging' => true,
            'pageLength' => 15,
            'scrollX' => false,
            'autoFill' => false,
        ];
        return $result;
    }
}
