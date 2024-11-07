<?php

use Illuminate\Database\Seeder;

class FintechCompanyCharges extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fintech_companies')->insert(array(
            array('id' => 1, 'company_name' => 'Payfast', 'status' => 1, 'added_by' => 1, 'updated_by' => 1),
            array('id' => 2, 'company_name' => '1 link', 'status' => 1, 'added_by' => 1, 'updated_by' => 1),
            array('id' => 3, 'company_name' => 'HBL', 'status' => 1, 'added_by' => 1, 'updated_by' => 1),
        ));

        DB::table('fintech_company_charges')->insert(array( 
            array(
                'id'                               =>  1, 
                'company_Id'                       => '1', 
                'range_up'                         => '0',
                'range_down'                       => '10000000', 
                'charges'                          => '1.8',
                'charges_is_percentage'            => '1',
                'additional_charges'               => '0', 
                'additional_charges_is_percentage' => '0',
                'fed_tax'                          => '13',
                'fed_tax_is_percentage'            => '1',
                'payment_type_id'                  => '1',
            ),
            array(
                'id'                               =>  2, 
                'company_Id'                       => '1', 
                'range_up'                         => '0',
                'range_down'                       => '10000000', 
                'charges'                          => '1.4',
                'charges_is_percentage'            => '1',
                'additional_charges'               => '0', 
                'additional_charges_is_percentage' => '0',
                'fed_tax'                          => '13',
                'fed_tax_is_percentage'            => '1',
                'payment_type_id'                  => '2',
            ),
            array(
                'id'                               =>  3, 
                'company_Id'                       => '1', 
                'range_up'                         => '0',
                'range_down'                       => '10000000', 
                'charges'                          => '1.4',
                'charges_is_percentage'            => '1',
                'additional_charges'               => '0', 
                'additional_charges_is_percentage' => '0',
                'fed_tax'                          => '13',
                'fed_tax_is_percentage'            => '1',
                'payment_type_id'                  => '3',
            ),
            array(
                'id'                               =>  4, 
                'company_Id'                       => '2', 
                'range_up'                         => '0',
                'range_down'                       => '10000', 
                'charges'                          => '10',
                'charges_is_percentage'            => '0',
                'additional_charges'               => '0', 
                'additional_charges_is_percentage' => '0',
                'fed_tax'                          => '0',
                'fed_tax_is_percentage'            => '0',
                'payment_type_id'                  => '2',
            ),
            array(
                'id'                               =>  5, 
                'company_Id'                       => '2', 
                'range_up'                         => '10001',
                'range_down'                       => '100000', 
                'charges'                          => '25',
                'charges_is_percentage'            => '0',
                'additional_charges'               => '0', 
                'additional_charges_is_percentage' => '0',
                'fed_tax'                          => '0',
                'fed_tax_is_percentage'            => '0',
                'payment_type_id'                  => '2',
            ),
            array(
                'id'                               =>  6, 
                'company_Id'                       => '2', 
                'range_up'                         => '100001',
                'range_down'                       => '250000', 
                'charges'                          => '50',
                'charges_is_percentage'            => '0',
                'additional_charges'               => '0', 
                'additional_charges_is_percentage' => '0',
                'fed_tax'                          => '0',
                'fed_tax_is_percentage'            => '0',
                'payment_type_id'                  => '2',
            ),
            array(
                'id'                               =>  7, 
                'company_Id'                       => '2', 
                'range_up'                         => '250001',
                'range_down'                       => '1000000', 
                'charges'                          => '100',
                'charges_is_percentage'            => '0',
                'additional_charges'               => '0', 
                'additional_charges_is_percentage' => '0',
                'fed_tax'                          => '0',
                'fed_tax_is_percentage'            => '0',
                'payment_type_id'                  => '2',
            ),
            array(
                'id'                               =>  8, 
                'company_Id'                       => '2', 
                'range_up'                         => '1000001',
                'range_down'                       => '10000000', 
                'charges'                          => '200',
                'charges_is_percentage'            => '0',
                'additional_charges'               => '0', 
                'additional_charges_is_percentage' => '0',
                'fed_tax'                          => '0',
                'fed_tax_is_percentage'            => '0',
                'payment_type_id'                  => '2',
            ),
            array(
                'id'                               =>  9, 
                'company_Id'                       => '3', 
                'range_up'                         => '0',
                'range_down'                       => '10000000', 
                'charges'                          => '20',
                'charges_is_percentage'            => '0',
                'additional_charges'               => '0', 
                'additional_charges_is_percentage' => '0',
                'fed_tax'                          => '13',
                'fed_tax_is_percentage'            => '1',
                'payment_type_id'                  => '2',
            ),
            array(
                'id'                               =>  10, 
                'company_Id'                       => '3', 
                'range_up'                         => '0',
                'range_down'                       => '10000000', 
                'charges'                          => '20',
                'charges_is_percentage'            => '0',
                'additional_charges'               => '0', 
                'additional_charges_is_percentage' => '0',
                'fed_tax'                          => '13',
                'fed_tax_is_percentage'            => '1',
                'payment_type_id'                  => '3',
            ),
        ));
    }
}
