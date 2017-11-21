<?php

        //封装mysqli连接数据库
        class connect_db{
            private  $host;
            private  $admin;
            private  $pwd;
            public $link;
            
            public function __construct($host,$admin,$password){           
                $this->host=$host;
                $this->admin=$admin;
                $this->pwd=$password;
                $this->link=$this->connect();
                    
                
            } 
            
            
            private function connect(){
                $link=new mysqli($this->host,$this->admin,$this->pwd);
                if($link->connect_errno){
                    echo "Connect error: ".$link->connect_error;
                    exit();
                }
                return $link;
         
            }
            
            public function close(){
                $this->link->close();                           
            }
  
            /**
             * 选择数据库 若数据库不存在提示错误
             * @param string $db
             *
             */
            public function selectdb($db){
                
                if(!$this->link->select_db($db)){
                    echo "ERROR:THE DATABASE {$db} IS NOT EXIST!";
                }
                
            }
            
            
            
            
            
            
            /**
             * 删除操作
             * @param string $table
             * @param string $con
             * @param string $val
             */
            public function delete($table,$con="",$val=""){
                $query="delete from ".$table;
                if($con!=""&&$val!=""){
                    $query.=" where ".$con."= '".$val."'";
                }
              
                   $pre=$this->link->prepare($query);
                   //return $query;
                  if($pre->execute())
                  {
                      return "delete successfully";
                  }else{
                      return "delete failed!";
                  }
                
                    
            }
            
            
         
            /**
             * 
             * @param string $table
             * @param array $col
             * @param array  $val
             * @return string 
             */
            public function insert($table,$col="",$val){
                $query="insert into {$table} ";
                //如果传递了字段变量，并且为数组
                if($col!=""&&is_array($col)){
                $length=count($col);
               
                if($length!=0){
                    $query.="(";
                    for($i=0;$i<$length;$i++){
                        if($i==$length-1){
                            $query.=$col[$i].")";
                        }else{
                            $query.=$col[$i].",";
                        }
                    }
                }
                
                
                
                
                }
                
                
                
            if(!is_array($val)){
                return "参数错误！";
            }else{
                $length2=count($val); 
                $query.=" values(";
                for($j=0;$j<$length2;$j++){
                    if($j==$length2-1){
                        $query.="'".$val[$j]."')";
                    }else{
                        $query.="'".$val[$j]."',";
                    }
                }
                
            }
                
                $pre=$this->link->prepare($query);
               if($pre->execute()){
                   return "insert successfully";
               }else{
                   return "insert error!";
               }
                           
            }
            
            /** 
             * 待改进：目前最后两个参数的数组长度必须相等
             * 
             * @param string $table
             * @param array $col
             * @param array $val
             * @param array $col2
             * @param array $val2
             *  
             * @return string
             *  
             * 
             */
            public function update($table,$col,$val,$col2="",$val2=""){
                //判断设置参数是否为数组并且不为空
                if(is_array($col)&&is_array($val)&&count($col)>0&&count($val)>0)
                {
                      $l1=count($col);
                      $l2=count($val);
                    $query="update {$table} set ";
                    
                    //如果只更新一个字段，则直接更新
                    if($l1==1){
                        $query.=$col[0]."='".$val[0]."'";
                    }elseif ($l1>$l2){
                        //字段参数数量大于值参数数量，剩余的赋null
                        
                        while(current($val)){
                           $query.=current($col)."='".current($val)."',";
                           next($col);
                           next($val);
                        }
                        $query.=current($col)."=null";
                        
                    }else{//字段数目小于值的数目
                        while(current($col)){
                            $query.=current($col)."='".current($val)."',";
                            next($val);
                            if(!next($col))
                                rtrim($query,",");
                            
                        }
                        
                        
                        
                    }
                    
                    
                     //判断传递的条件参数
                    if(is_array($col2)&&is_array($val2)&&count($col2)>0&&count($val2)>0){
                       while(current($col2)&&current($val2)){
                           
                           $query.=" where ".current($col2)."=".current($val2)." and";
                           if(!next($col2)){
                               $query=rtrim($query,"and");
                           }                         
                           next($val2);
                       }                  
                    }
             
                
                    
                    
                }else{
                   return "更新失败：参数错误！！！";
                }
                
                $pre=$this->link->prepare($query);
                if($pre->execute()){
                    echo "更新成功";
                    return ;
                }else{
                    echo "更新失败";
                    return ;
                }
                
            }
            
            
               /**
             * 
             *查询功能
             * @param string $table
             * @param array $col
             * @param array $con
             * @param array $val
             * @return string|boolean
             */
            public function select($table,$col,$con="",$val=""){
                if(is_array($col)){
                    $l1=count($col);
                    //判断字段参数
                    if($l1==1){
                        $query="select ".$col[0]." from {$table}";
                    }else{
                        $query="select ";
                        while(current($col)){
                           
                            $query.=current($col).",";
                            if(!next($col)){
                                $query=rtrim($query,",");
                                $query.=" from {$table}";
                            }
                        }
                        
                    }
                        
                    //判断条件参数
                        if(is_array($con)&&is_array($val)&&count($con)>0&&count($val)>0){
                            $l2=count($con);
                            $l3=count($val);                        
                            if($l2<$l3){
                                return "参数错误！";
                            }else{
                                while(current($con)){
                                    $query.=" where ".current($con)."=".current($val)."and";
                                    if(!next($con)) $query=rtrim($query,"and");
                                 
                                    next($val);
                                }
                            }
                     
                        }
                        $re=$this->link->query($query);
                        $result=mysqli_fetch_row($re);
                        var_dump($result);
                        
                        }else{
                            return false;
                        } 
          
            }
            
      
        }
