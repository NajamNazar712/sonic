<?php

use Illuminate\Database\Seeder;

class DeleteInvoiceNamesFromAdminScreenListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins_screen_list')->where('name','Financials  >  Invoices  >  Received Invoices')->delete();
        DB::table('admins_screen_list')->where('name','Financials > COD Payments > Reimbursement Invoice')->delete();
        DB::table('admins_screen_list')->where('name','Financials  >  Invoices  >  Pending Invoices')->update(['name' => 'Financials  >  Invoices  > Invoices']);
    }
}
