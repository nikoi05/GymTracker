<?php 
    require_once __DIR__ . "../../BL/userManager.php";
    $userM = new managerUser();
    


    $type=$_GET['type'] ?? 'weekly'; // from services
    
   
    if($type ==='monthly'){
        $data=$userM->getMonthlyRegistration();
    }else{
        $data=$userM->getweeklyRegistration();
    }
    $labels=[];
    $values=[];

    if(is_array($data) && count($data)>0){
         foreach($data as $row){
    $labels[]=$row['label'] ??'Unknown';
    $values[]=(int)$row['total'];
    }
    }
   
    header("Content-Type: application/json");
    
    echo json_encode([
        "labels"=>$labels,
        "values"=>$values
    ]);
    exit;
?>