<?php

use Illuminate\Database\Seeder;

class RiderUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rider_id = array("Trax00117","Trax00333","Trax00433","Trax00434","Trax00494","Trax00749","Trax00750","Trax00751","Trax00794","Trax00811","Trax00815","Trax00816","Trax00817","Trax00837","Trax00880","Trax00887","Trax00888","Trax00890","Trax00942","Trax00947","Trax01049","Trax01137","Trax01138","Trax01194","Trax01195","Trax01255","Trax01303","Trax01305","Trax01448","Trax01460","Trax01461","Trax01464","Trax01465","Trax01468","Trax01599","Trax01617","Trax01618","Trax01620","Trax01621","Trax01622","Trax01623","Trax01627","Trax01631","Trax01633","Trax01635","Trax01637","Trax01640","Trax01978","Trax01979","Trax01981","Trax02074","Trax02081","Trax02083","Trax02084","Trax02101","Trax02195","Trax02263","Trax02264","Trax02265","Trax02266","Trax02267","Trax02269","Trax02270","Trax02329","Trax02331","Trax02333","Trax02476","Trax02478","Trax02479","Trax02480","Trax02484","Trax02485","Trax02507","Trax02628","Trax02639","Trax02761","Trax02762","Trax02764","Trax02767","Trax02768","Trax02972","Trax02977","Trax02980","Trax02981","Trax02982","Trax02984","Trax02985","Trax02990","Trax02995","Trax03002","Trax03009","Trax03100","Trax03102","Trax03103","Trax03563","Trax03577","Trax03847","Trax03850","Trax03851","Trax03852","Trax03853","Trax03866","Trax03887","Trax03930","Trax03955","Trax04040","Trax04041","Trax04049","Trax04053","Trax04152","Trax04170","Trax04183","Trax04237","Trax04239","Trax04241","Trax04244","Trax04245","Trax04247","Trax04254","Trax04311","Trax04335","Trax04344","Trax04345","Trax04346","Trax04361","Trax04375","Trax04381","Trax04384","Trax04389","Trax04393","Trax04398","Trax04421","Trax04422","Trax04425","Trax04426","Trax04427","Trax04429","Trax04441","Trax04442","Trax04447","Trax04450","Trax04451","Trax04458","Trax04463","Trax04479","Trax04555","Trax04556","Trax04557","Trax04565","Trax04566","Trax04581","Trax04587","Trax04589","Trax04599","Trax04627","Trax04628","Trax04632","Trax04633","Trax04647","Trax04693","Trax04700","Trax04702","Trax04704","Trax04706","Trax04707","Trax04708","Trax04710","Trax04713","Trax04734","Trax04745","Trax04751","Trax04760","Trax04776","Trax04777","Trax04905","Trax04907","Trax04908","Trax04921","Trax04923","Trax05225","Trax05316","Trax05333","Trax05364","Trax05410","Trax05413","Trax05417","Trax05500","Trax05517","Trax05518","Trax05519","Trax05520","Trax05521","Trax05523","Trax05535","Trax05546","Trax05564","Trax05565","Trax05613","Trax05614","Trax05616","Trax05617","Trax05620","Trax05652","Trax05653","Trax05654","Trax05669","Trax05670","Trax05673","Trax05674","Trax05678","Trax05684","Trax05685","Trax05687","Trax05689","Trax05692","Trax05696","Trax05739");

        foreach ($rider_id as $id)
        {
            $rider = \App\Http\Models\Rider::where('trax_id', $id);
            if($rider->exists()){
                $rider = $rider->first();
                $rider->ccd = 1;
                $rider->save();
            }
        }
    }
}
