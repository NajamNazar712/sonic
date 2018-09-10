<?php

use Illuminate\Database\Seeder;

class BanksListTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks_lists')->truncate();

        $codes = array('AIIN','ABPA','AEIB','ASCM','BAHL','ALFH','KHYB','BPUN','BOTK','BKIP','BARC','BURJ','CDCP','CITI','DEUT','DUIB','FAIB','FAYS','FWOM','HABB','MPBL','OIBA','IFBL','ICBK','JSBL','PLCO','MUCB','MCIB','MEZN','NBPA','NIBP','RUPB','MBPL','SILK','PRUC','SIND','SMES','SONE','SCBL','SBPP','BCEY','ABNA','TRBA','TIBL','UNIL');

        $names = array(
            'Albaraka Bank (Pakistan) Limited',
            'Allied Bank Limited',
            'American Express Bank Limited',
            'Askari Bank Limited',
            'Bank Al Habib Limited',
            'Bank Alfalah Limited',
            'Bank Of Khyber',
            'The Bank Of Punjab',
            'The Bank Of Tokyo-Mitsubishi Ufj Limited',
            'The BankIslami Pakistan Limited',
            'Barclays Bank Plc',
            'Burj Bank Limited',
            'Central Depository Company Of Pakis Tan Limited',
            'Citibank N. A. Pakistan',
            'Deutsche Bank Ag Karachi Branch',
            'Dubai Islamic Bank Pakistan Limited',
            'Faisal Islamic Bank Of Bahrain',
            'Faysal Bank Limited',
            'First Women Bank Limited',
            'Habib Bank Limited',
            'Habib Metropolitan Bank Limited',
            'HSBC Bank Oman S.A.O.G. (Formerly Oman International Bank)',
            'IFIC Bank Limited',
            'Industrial And Commercial Bank Of China Karachi Branch',
            'JS Bank Limited',
            'Kasb Bank Limited',
            'Mcb Bank Limited',
            'Mcb Islamic Bank',
            'Meezan Bank Limited',
            'National Bank Of Pakistan',
            'NIB Bank Limited',
            'Rupali Bank Limited',
            'Samba Bank Limited',
            'Silk Finance',
            'Silkbank Limited',
            'Sindh Bank Limited',
            'Sme Bank Limited',
            'Soneri Bank Limited',
            'Standard Chartered Bank (Pakistan) Limited',
            'State Bank Of Pakistan',
            'Summit Bank Limited',
            'The Royal Bank Of Scotland Limited',
            'Trust Bank Limited',
            'Trust Investment Bank Limited',
            'United Bank Limited
        ');

        $affilate = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

        for($i=0;$i<count($names);$i++)
        {
            DB::table('banks_lists')->insert(array(
                array('name' => $names[$i], 'code' => $codes[$i], 'affiliate' => $affilate[$i], 'status' => 1),
            ));
        }
    }
}
