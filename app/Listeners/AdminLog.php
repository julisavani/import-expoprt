<?php

namespace App\Listeners;

use App\Events\AdminLogStored;
use App\Models\Admin;
use App\Models\AdminLog as ModelsAdminLog;
use App\Models\Appointment;
use App\Models\Clarity;
use App\Models\Color;
use App\Models\Country;
use App\Models\Demand;
use App\Models\DiamondCart;
use App\Models\DiamondConfirm;
use App\Models\DiamondHold;
use App\Models\DiamondInquiry;
use App\Models\DiamondRequest;
use App\Models\FancyColor;
use App\Models\Finish;
use App\Models\Fluorescence;
use App\Models\Marketing;
use App\Models\Merchant;
use App\Models\Policy;
use App\Models\Product;
use App\Models\SaveSearch;
use App\Models\Shape;
use App\Models\Size;
use App\Models\Slot;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AdminLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AdminLogStored $event): void
    {
        $Log = $event->Log;
        $log_entrytype = $Log['log_entrytype'];
        $log_id = $Log['log_id'];
        $user_id = $Log['user_id'];
        $LogType = $Log['LogType'];
        $AdminType = $Log['AdminType'];
        $LogName = $Log['LogName'];
        $LName = $Log['LName'];
        if($LogType == 13 || $LogType == 14 || $LogType == 15 || $LogType == 16 || $LogType == 17 || $LogType == 21) {
            $product_id = $Log['product_id'];
        }

        if($log_entrytype == "1") { // edit
            unset($Log['log_id']);
            unset($Log['log_entrytype']);
            unset($Log['user_id']);
            unset($Log['LogType']);
            unset($Log['LogName']);
            unset($Log['LName']);
            unset($Log['AdminType']);
            if($LogType == 13 || $LogType == 14 || $LogType == 15 || $LogType == 16 || $LogType == 17 || $LogType == 21) {
                unset($Log['product_id']);
            }
            if($LogType == 1){
                if($AdminType == 1) {
                    $oldlog = Admin::find($log_id);
                } else {
                    $oldlog = User::find($log_id);
                }
            }
            if($LogType == 2){
                $oldlog = User::find($log_id);
            }
            if($LogType == 3){
                $oldlog = Clarity::find($log_id);
            }
            if($LogType == 4){
                $oldlog = Color::find($log_id);
            }
            if($LogType == 5){
                $oldlog = Size::find($log_id);
            }
            if($LogType == 6){
                $oldlog = Shape::find($log_id);
            }
            if($LogType == 7){
                $oldlog = FancyColor::find($log_id);
            }
            if($LogType == 8){
                $oldlog = Finish::find($log_id);
            }
            if($LogType == 9){
                $oldlog = Fluorescence::find($log_id);
            }
            if($LogType == 10){
                $oldlog = Policy::find($log_id);
            }
            if($LogType == 11){
                $oldlog = Marketing::find($log_id);
            }
            if($LogType == 12){
                $oldlog = Slot::find($log_id);
            }
            if($LogType == 13){
                $oldlog = DiamondRequest::find($log_id);
            }
            if($LogType == 14){
                $oldlog = DiamondHold::find($log_id);
            }
            if($LogType == 15){
                $oldlog = DiamondCart::find($log_id);
            }
            if($LogType == 16){
                $oldlog = DiamondConfirm::find($log_id);
            }
            if($LogType == 17){
                $oldlog = DiamondInquiry::find($log_id);
            }
            if($LogType == 18){
                $oldlog = Appointment::find($log_id);
            }
            if($LogType == 19){
                $oldlog = Demand::find($log_id);
            }
            if($LogType == 20){
                $oldlog = SaveSearch::find($log_id);
            }
            if($LogType == 21){
                $oldlog = Product::find($product_id);
            }
            if($LogType == 22){
                $oldlog = Vendor::find($log_id);
            }
            if($LogType == 23){
                $oldlog = Merchant::find($log_id);
            }
            foreach ($Log as $key => $value) {
                if($oldlog->$key != $Log[$key]) {
                    $oldval = (string)$oldlog->$key;
                    $newval = $value;
                    $data = [];
                    if($AdminType == 1) {
                        $data['admin_id'] = $user_id;
                    } else{
                        $data['user_id'] = $user_id;
                    }

                    if($key == "status"){
                        if($value == 1){
                            $oldval = "Deactive";
                            $newval = "Active";
                        } else{
                            $oldval = "Active";
                            $newval = "Deactive";
                        }
                    }

                    if($key == "country_id") {
                        $oldleg = Country::find($oldval);
                        $oldval = $oldleg->name ?? '';
                        $newleg = Country::find($newval);
                        $newval = $newleg->name ?? '';
                    }

                    $data['event'] = $LogName ." Changed ".$key." for " . $newval;
                    $data['old_value'] = (string)$oldval;
                    $data['new_value'] = $newval;
                    $data['log_type'] = 1;
                    $data['log_id'] = $log_id;
                    if($LogType == 13 || $LogType == 14 || $LogType == 15 || $LogType == 16 || $LogType == 17 ) {
                        $data['product_id'] = $product_id;
                    }
                    ModelsAdminLog::create($data);
                }
            }
        } else if($log_entrytype == "0") { // create / Delete / status
            $data = [];
            if($AdminType == 1) {
                $data['admin_id'] = $user_id;
            } else{
                $data['user_id'] = $user_id;
            }
            $data['event'] = $LName." ". $LogName;
            $data['log_type'] = $LogType;
            $data['log_id'] = $log_id;
            if($LogType == 21) {
                $data['product_id'] = $product_id;
            }
            ModelsAdminLog::create($data);
        }
    }
}
