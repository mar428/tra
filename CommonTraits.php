<?php 


namespace App\Traits;


use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use PDF;
use View;
use Image;


use App\Models\CategoryFilterAttrib;

use App\Models\Profile;
use App\Models\BiodataPot;
use App\Models\Member;
use App\Models\SoftConfig;
use App\Models\FuncPackList;
use App\Models\PointTranscations;



trait CommonTraits {

    


    public function commonsearch(Request $res,$id){

        $myprofile=Profile::where('proid',$id)->first();
        $gen=$myprofile->gender_profile;
        $ss = $res->prof_bio;
        $myprofile_bio=BiodataPot::where('proid_bio',$id)->get();
        $myfe = $myprofile_bio->pluck('fe_id_bio');
        $profile=BiodataPot::where('proid_bio',"!=",$id)->whereHas('profile', function($q)use ($gen){
            $q->where('profile.gender_profile','=',$gen);
        })->whereHas('perfpropot', function($k)use ($myfe){
            $k->whereIn('perfpro_pot.fe_id_perf',$myfe);
        })->whereIn('fe_id_bio',$ss)->get();

        $pro = $profile->pluck('proid_bio');

        return $myprof=Profile::whereIn('proid',$pro)->get();
        
    }

    

    public function adminadvancedsearch(Request $res){

       // $myprofile=Profile::first();
      //  $gen=$myprofile->gender_profile;

        $ss = $res->prof_bio;
        $myprofile_bio=BiodataPot::get();
        $myfe = $myprofile_bio->pluck('fe_id_bio');
        $profile=BiodataPot::whereHas('profile')->whereHas('perfpropot', function($k)use ($myfe){
            $k->whereIn('perfpro_pot.fe_id_perf',$myfe);
        })->whereIn('fe_id_bio',$ss)->get();

        $pro = $profile->pluck('proid_bio');

        $myprof=Profile::whereIn('proid',$pro)->get();
        $arr = array('msg' => "Profile Add UnSuccessfully" , 'status' => false,'result'=>'faild','profile'=>$myprof);
    
        return $myprof;
  
    }

    
    public function multiFileUpload(Request $request,$path="prof_image_encrpted")
    {

        $files=[];
        //$destinationPath = 'F:\web_app\htdocs\lara\asset\photo_gallary';

        $destinationOriginalPath=public_path('image/prof_image_orgin');
        $destinationEncrpPath=public_path('image/prof_image_encrpted');
        $destinationDisplayPath=public_path('image/prof_image_display');

        if($request->hasfile('file'))
        {
           
            foreach($request->file('file') as $file)
            {
                
                $name = time().'_'. pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$file->extension();
                $file->move($destinationOriginalPath, $name); 

                $comprofile=SoftConfig::first();
                $wa_path = storage_path('app/public/logo/'.$comprofile->photo_water_mark_config);

                $img = Image::make($destinationOriginalPath.'/'.$name);
                $img->insert($wa_path, 'center', 600, 600);
                $img->save($destinationDisplayPath.'/'.$name);
                $img->blur(50);
                $img->save($destinationEncrpPath.'/'.$name);

               // $filePath = $file->storeAs($path, $name, 'public');
                $files[]=$name;
                $arr = array('msg' => "Profile Add Successfully" , 'status' => true,'result'=>'success','files'=>$files);

            }
            
             $arr = array('msg' => "Profile Add Successfully" , 'status' => true,'result'=>'success','files'=>$files);

         }else{
            $arr = array('msg' => "Profile Add UnSuccessfully" , 'status' => false,'result'=>'faild','files'=>$files);
         }
        return Response()->json($arr);
    }


    public function photoFileUpload(Request $request,$path="prof_image_encrpted")
    {

        $files=[];
        //$destinationPath = 'F:\web_app\htdocs\lara\asset\photo_gallary';

        $destinationOriginalPath=public_path('image/prof_image_orgin');
        $destinationEncrpPath=public_path('image/prof_image_encrpted');
        $destinationDisplayPath=public_path('image/prof_image_display');

        if($request->hasfile('file'))
        {
            
                $file=$request->file('file');
       
                $name = time().'_'. pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$file->extension();
                $file->move($destinationOriginalPath, $name); 

                $comprofile=SoftConfig::first();
                $wa_path = storage_path('app/public/logo/'.$comprofile->photo_water_mark_config);

                $img = Image::make($destinationOriginalPath.'/'.$name);
                $img->insert($wa_path, 'center', 600, 600);
                $img->save($destinationDisplayPath.'/'.$name);
                $img->blur(50);
                $img->save($destinationEncrpPath.'/'.$name);

               // $filePath = $file->storeAs($path, $name, 'public');
                $files[]=$name;
                $arr = array('msg' => "Profile Add Successfully" , 'status' => true,'result'=>'success','files'=>$files);


         }else{

            $arr = array('msg' => "Profile Add UnSuccessfully" , 'status' => false,'result'=>'faild');

         }

        return Response()->json($arr);

    }



  

    public function fileUpload(Request $req,$post_key,$path="other_image"){

    
        if($req->hasFile($post_key)) {

            $fileName = time().'_'.$req->file($post_key)->getClientOriginalName();
            $filePath = $req->file($post_key)->storeAs($path, $fileName, 'public');

           
            if($filePath){

                $arr = array('msg' => "Done",'path'=> $fileName, 'status' => true,'result'=>'success');
            
            }else{
                
                $arr = array('msg' => "File Upload Faild",'path'=> $fileName,'status' => false,'result'=>'faild');

            }
                
        }else{

            $arr = array('msg' => "File Upload Empty",'status' => true,'result'=>'success');
        
        }
        return Response()->json($arr);
   }


   public function webadvancedsearch(Request $res,$id=""){

    // $myprofile=Profile::first();
    // $gen=$myprofile->gender_profile;

        $mem_data=Member::where('userid_member',($id=="")? Auth::id():$id)->first();
         
        $myprofile_bio=BiodataPot::where('proid_bio',$mem_data->current_act_profile_member)->get();
        $myfe = $myprofile_bio->pluck('fe_id_bio');

        // $profile=BiodataPot::whereHas('profile')->distinct()->whereHas('perfpropots', function($k) use ($myfe){
        //     $k->distinct()->whereIn('perfpro_pot.fe_id_perf',$myfe);
        // })->whereIn('fe_id_bio',$ss)->get();

         $profiles=BiodataPot::whereHas('profile');

        foreach($myfe as $feId){
            $profiles->whereHas('perfpropot', function($q) use ($feId){
                $q->where('perfpro_pot.fe_id_perf', $feId);
            });
        }
        
        if(isset($res)){
            $ss = $res->prof_bio;
            $profile=$profiles->whereIn('fe_id_bio',$ss)->get();

        }else{
            $profile=$profiles->get();
        }
       
        $sspro = $profile->pluck('proid_bio');
        $pro = $profile->pluck('proid_bio');

    /*  $members_act=Member::where('memid',$pro)->with('memact_list')->get(); */

        $myprof=Profile::whereIn('proid',$pro)->whereNotIn('memid_profile',[$mem_data->memid])->with('profileimg',
            function($im){
                $im->where('profile_photos.profile_photo','1');
            } 
        )->with('filterentities')->with('filterentities.subcategoryattrib')->with('memactivitylist.typeactivity')->get();
       

        
        if($myprof->isNotEmpty()){
        
            $arr = array( 'msg' => "Done", 'status' => true,'value' => $ss ,'profile'=>$myprof);                                 
    
        }else{

            $arr = array( 'msg' => "Done", 'status' => false,'value' => $myfe ,'profile'=>$pro);                                 
    
        }
        
        return $arr;

    }

    
 


    public function webadvancedsearchall($id=""){

            // $myprofile=Profile::first();
            // $gen=$myprofile->gender_profile;
            
            //  $ss = $res->prof_bio;
            //$myprofile_bio=BiodataPot::get();

            $mem_data=Member::where('userid_member',($id=="")? Auth::id():$id)->first();
            
            $myprofile_bio=BiodataPot::where('proid_bio',$mem_data->current_act_profile_member)->get();
            $myfe = $myprofile_bio->pluck('fe_id_bio');

            $profile=BiodataPot::whereHas('profile')->whereHas('perfpropot', function($k)use ($myfe){
                $k->whereIn('perfpro_pot.fe_id_perf',$myfe);
            })->get();

            $pro = $profile->pluck('proid_bio');

        /*  $members_act=Member::where('memid',$pro)->with('memact_list')->get(); */

            $myprof=Profile::whereIn('proid',$pro)->whereNotIn('memid_profile',[$mem_data->memid])->whereNotIn('memid_profile',[$mem_data->memid])->with('profileimg',
                function($im){
                    $im->where('profile_photos.profile_photo','1');
                } 
            )->with('filterentities')->with('filterentities.subcategoryattrib')->with('memactivitylist.typeactivity')->get();
        
            if($myprof->isNotEmpty()){
            
                $arr = array( 'msg' => "Done", 'status' => true,'value' => "" ,'profile'=>$myprof);                                 
            
            }else{

                $arr = array( 'msg' => "Done", 'status' => false,'value' =>"" ,'profile'=>$myprof);                                 
            
            }
            
            return $arr;
    
        }

        
        public function filter(){

        }

    public function apiadvancedsearch(Request $req){

        // $myprofile=Profile::first();
        // $gen=$myprofile->gender_profile;
    
        $ss = $req->email;
        $myprofile_bio=BiodataPot::get();
        $myfe = $myprofile_bio->pluck('fe_id_bio');
        $profile=BiodataPot::whereHas('profile')->whereHas('perfpropot', function($k)use ($myfe){
            $k->whereIn('perfpro_pot.fe_id_perf',$myfe);
        })->whereIn('fe_id_bio',$ss)->get();

        $pro = $profile->pluck('proid_bio');
        /*  $members_act=Member::where('memid',$pro)->with('memact_list')->get(); */
        $myprof=Profile::whereIn('memid_profile',$pro)->with('profileimg')->with('members.shortlisted')->get();
        $arr = array('value' => $ss ,'profile'=>$myprof);                                 

    return $arr;

  }

   public function getJson($arr){

    $con = $arr->getContent();
    return json_decode($con , true);

   }

   public function getArrayFromJson($arr){

    $res_bio1[]=array();
    $res_bio1=json_decode($arr, true);
    return $res_bio1;

   }

  

   public function SMS($num,$msg) {				     
        error_reporting (E_ALL ^ E_NOTICE);        
        $username="ramelx";        
        $password ="elixirmix459";        
        $number=$num;        
        $sender="TESTID";        
        $message=$msg;    
        $template_id='0';           
        
        $url="http://api.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($number)."&sender=".urlencode($sender)."&message=".urlencode($message)."&type=".urlencode('3')."&template_id=".urlencode($template_id);         
        $ch = curl_init($url);        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);        
        $curl_scraped_page = curl_exec($ch);        
        curl_close($ch);       
        return  $curl_scraped_page;

    }

   public function getMenuFE(){
        $cfa=CategoryFilterAttrib::with('subcategory')->get();
        View::share([
            'filter_entity',$cfa
        ]);
    }

    public function memmmid($id)
    {

        
        $data_request = [  
            'mmid_member' => "MM00".$id,
        ];

        $insert_mem_data=Member::where('memid',$id)->update($data_request);

        if($insert_mem_data){       
            return true;
        }else{
            return false;
        }


    }

    public function pointDetection($slug,$id=""){

        $mem_data=Member::where('userid_member',($id=="")? Auth::id():$id)->first();
        $mempak= $mem_data->current_pkg_member;
        $func_list=FuncPackList::where('pak_id',$mempak)->with('functionality')->whereHas('functionality',function($q) use ($slug){
            $q->where('func_nality_slug', $slug);
        })->first();

        if($func_list){
              $func_point=$func_list->functionality->func_nality_valuepoints;
        }else{
            $func_point=0;
            $arr = array( 'msg' => "Upgrade Your Package.", 'status' => false,'value' => "" );                                 
           
        }

        if($mem_data->balance_points_member<$func_point){

            $arr = array( 'msg' => "Insufficient Balance.", 'status' => false,'value' => "" );                                                                  
           
        }else{

            $membal= $mem_data->balance_points_member-$func_point;

            $data_request = [  
                'balance_points_member' => $membal,
            ];

            $insert_mem_data=Member::where('userid_member',($id=="")? Auth::id():$id)->update($data_request);
            if($insert_mem_data){
                $arr = array( 'msg' => "Done", 'status' => true,'value' => "" ); 

                $data_points_request = [  
                    'mem_point_transc' => $mem_data->memid,
                    'profile_point_transc' => $mem_data->current_act_profile_member,
                    'func_id_transc' =>$func_list->functionality->func_nality_id,
                    'func_name_transc' => $func_list->functionality->func_nality_name,
                    'func_amount_transc' => $func_list->functionality->func_nality_valuepoints,
                    "created_at" =>  \Carbon\Carbon::now(),
                ];
                

                $update_points_request=PointTranscations::insert($data_points_request);
                
                
                $mem_data=Member::where('userid_member',($id=="")? Auth::id():$id)->first();
                View::share([
                    'loginmember'=>$mem_data,
                ]);
                                        
            }else{
                $arr = array( 'msg' => "Insufficient Balance.", 'status' => false,'value' => "" );                                                                  
           
            }

        }

        return $arr;

    }

}
?>