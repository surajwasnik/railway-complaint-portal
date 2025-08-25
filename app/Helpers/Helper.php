<?php

namespace App\Helpers;

use App\Models\Complaint;
use App\Models\Station;

class Helper
{

    public static function getMessage(
        string $status,
        string $complainantName,
        string $firNumber,
        string $date,
        string $policeStation,
        string $officerName,
        string $ioContact
    ): array {
        $english = '';
        $marathi = '';

        switch ($status) {

            case '1':
                $english = "Dear {$complainantName},\nYour FIR No. {$firNumber}, dated {$date}, registered at {$policeStation} is under investigation. You will be updated on further progress.\n- {$officerName}";
                
                $marathi = "आदरणीय {$complainantName},\n आपली FIR क्रमांक {$firNumber}, दिनांक {$date}, पोलीस ठाणे {$policeStation} येथे नोंद असून चौकशी सुरू आहे. पुढील प्रगतीची माहिती आपणास कळविण्यात येईल.\n - {$officerName}";

            case '2':
                $english = "Dear {$complainantName},\nYour FIR No. {$firNumber}, dated {$date}, registered at {$policeStation} is under investigation. You will be updated on further progress.\n- {$officerName}";

                $marathi = "आदरणीय {$complainantName},\nआपली FIR क्रमांक {$firNumber}, दिनांक {$date}, पोलीस ठाणे {$policeStation} येथे नोंद असून चौकशी सुरू आहे. पुढील प्रगतीची माहिती आपणास कळविण्यात येईल.\n- {$officerName}";
                break;

            case '3':
                $english = "Dear {$complainantName},\nYour FIR No. {$firNumber}, dated {$date}, registered at {$policeStation} has been detected. The stolen property has been recovered.\nIf it is a mobile phone, IMEI has been traced and the handset can be collected after completing formalities. Please contact {$ioContact}.\n- {$officerName}";

                $marathi = "आदरणीय {$complainantName},\nआपली FIR क्रमांक {$firNumber}, दिनांक {$date}, पोलीस ठाणे {$policeStation} येथे नोंद असून प्रकरणाचा शोध लागला आहे. चोरीस गेलेले साहित्य मिळाले आहे.\nमोबाईल फोन असल्यास, IMEI शोधण्यात आला असून आवश्यक औपचारिकता पूर्ण करून मोबाईल ताब्यात घेण्यासाठी कृपया {$ioContact} यांच्याशी संपर्क साधा.\n- {$officerName}";
                break;

            case '4':
                $english = "Dear {$complainantName},\nYour FIR No. {$firNumber}, dated {$date}, registered at {$policeStation} has been detected, but the stolen property has not been recovered yet. Investigation is ongoing for recovery.\n- {$officerName}";

                $marathi = "आदरणीय {$complainantName},\nआपली FIR क्रमांक {$firNumber}, दिनांक {$date}, पोलीस ठाणे {$policeStation} येथे नोंद असून प्रकरणाचा शोध लागला आहे. परंतु चोरीस गेलेले साहित्य अद्याप मिळालेले नाही. साहित्य मिळविण्यासाठी पुढील चौकशी सुरू आहे.\n- {$officerName}";
                break;

            case '5':
                $english = "Dear {$complainantName},\nYour stolen mobile phone (FIR No. {$firNumber}, dated {$date}, registered at {$policeStation}) has been recovered.  \nKindly contact {$officerName}, {$policeStation}, {$ioContact} to complete formalities and collect your handset. \n-{$officerName}";

                $marathi = "आदरणीय {$complainantName},\nआपला चोरीस गेलेला मोबाईल फोन (FIR क्रमांक {$firNumber}, दिनांक {$date}, पोलीस ठाणे {$policeStation}) मिळाला आहे. \nकृपया आवश्यक औपचारिकता पूर्ण करून मोबाईल ताब्यात घेण्यासाठी {$officerName}, {$policeStation}, {$ioContact} यांच्याशी संपर्क साधा.\n- {$officerName}";
                break;

            case '6':
                $english = "Dear {$complainantName},\nYour FIR No. {$firNumber}, dated {$date}, at {$policeStation} could not be traced even after investigation. As per legal procedure, a Closure Report has been filed.\nFor more information, please contact {$policeStation} / {$officerName}.\n- {$officerName}";
                
                $marathi = "आदरणीय {$complainantName},\nआपली FIR क्रमांक {$firNumber}, दिनांक {$date}, पोलीस ठाणे {$policeStation} येथे नोंद असून चौकशीनंतरही प्रकरणाचा शोध लागू शकला नाही. कायदेशीर प्रक्रियेप्रमाणे बंद अहवाल (Closure Report) दाखल करण्यात आलेला आहे.\nअधिक माहितीसाठी कृपया {$policeStation} / {$officerName} यांच्याशी संपर्क साधा.\n- {$officerName}";
                break;


            default;

        }

       return [$english . "\n\n" . $marathi];
    }

    public static function getComplaints()
    {
        $user = auth()->user();
        if ($user->role_id == 2) {
            $stationIds = Station::where('user_id', $user->id)->pluck('id');
            $complaints = Complaint::whereIn('station_id', $stationIds)->get();
        } else {
            $complaints = Complaint::all();
        }
        return $complaints;
    }
}
