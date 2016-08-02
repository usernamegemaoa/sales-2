<?php
        
         $config=array();
         $config["mod_name"]="Sales";
         $config["mod_version"]="1.0";
         $config["mod_directory"]="sales";
         $config["mod_type"]="User";
         $config["mod_setup_class"]="TSetupSales";
         $config["mod_ui_name"]="Sales";
         $config["mod_ui_icon"]="sales-48.png";
         $conifg["mod_description"]="Module Sales";
         $config['mod_config'] = true;
         if (@$a == 'setup') {
	       echo dPshowModuleConfig($config );
         }

        class TSetupSales {

                public function install() {

                    $q = new DBQuery();

                    $define_table_arr = $this->define_table();
                    $tbl_arr = $define_table_arr['tbl'];
                    $sql_arr = $define_table_arr['sql'];

                    for ($i = 0; $i < count($tbl_arr); $i++) {
                        $this->runInstall($q, $sql_arr[$i], $tbl_arr[$i]);
                    }
                    
                }
                
                private function runInstall($q, $sql, $table) {
	            $q->createTable($table);
	            $q->createDefinition($sql);
	            $q->exec();
	            $q->clear();
                }

                public function remove() {
                    $t = new DBQuery;
                    $tbl_arr = $this->define_table('tbl'); // lay ra danh sach dinh nghia table
                    if (count($tbl_arr) > 0) {
                        foreach ($tbl_arr as $tbl) {
                            $t->dropTable($tbl);
                            $t->exec();
                            $t->clear();
                        }
                    }
                }
                
                function configure() {
                    global $AppUI;
                    $AppUI->redirect('m=sales&a=configure');	// load module specific configuration page
                    return true;
                }

                private function define_table($str_input = '') {

                    $table = array(); $sql = array();

	            $table[] = "sales_currency";
                    $sql[] = "(
                              `currency_id` int(11) NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `currency_name` varchar(100) NOT NULL,
                              `currency_symbol` varchar(50) NOT NULL,
                              PRIMARY KEY (`currency_id`)
				) ENGINE = MYISAM  ";


                    $table[] = "sales_header_default";
                    $sql[] = "(
                              `header_id` int(11) NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `header_contents` text NOT NULL,
                              PRIMARY KEY (`header_id`)
				) ENGINE = MYISAM  ";


                    $table[] = "sales_invoice";
                    $sql[] = "(
                              `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `quotation_id` int(11) NOT NULL,
                              `supplier_id` int(11) NOT NULL,
                              `customer_id` int(11) NOT NULL,
                              `address_id` int(11) NOT NULL,
                              `attention_id` int(11) NOT NULL,
                              `invoice_date` date NOT NULL,
                              `invoice_no` varchar(300) NOT NULL,
                              `invoice_sale_person` varchar(300) NOT NULL,
                              `invoice_sale_person_email` varchar(300) NOT NULL,
                              `invoice_sale_person_phone` varchar(300) NOT NULL,
                              `invoice_status` tinyint(4) NOT NULL,
                              `invoice_internal_notes` text NOT NULL,
                              `invoice_template` int(11) NOT NULL,
                              PRIMARY KEY (`invoice_id`)
				) ENGINE = MYISAM  ";

                    $table[] = "sales_invoice_customer_mapping";
                    $sql[] = "(
                              `sales_invoice_customer_mapping_id` int(11) NOT NULL AUTO_INCREMENT,
                              `invoice_id` int(11) NOT NULL,
                              `contact_or_attention_id` int(11) NOT NULL,
                              `customer_type` tinyint(1) NOT NULL,
                              PRIMARY KEY (`sales_invoice_customer_mapping_id`)
				) ENGINE = MYISAM  "; //customer_type '0: address, 1: attention'
                    
                    $table[] = "sales_invoice_item";
                    $sql[] = "(
                              `invoice_item_id` int(11) NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `invoice_id` int(11) NOT NULL,
                              `invoice_revision_id` int(11) NOT NULL,
                              `invoice_item` varchar(300) NOT NULL,
                              `invoice_item_price` float NOT NULL,
                              `invoice_item_quantity` int(4) NOT NULL,
                              `invoice_item_discount` int(4) NOT NULL,
                              `invoice_item_type` int(2) NOT NULL,
                              `invoice_item_notes` varchar(500) NOT NULL,
                              PRIMARY KEY (`invoice_item_id`)
				) ENGINE = MYISAM  ";


                    $table[] = "sales_invoice_revision";
                    $sql[] = "(
                              `invoice_revision_id` int(11) NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `invoice_id` int(11) NOT NULL,
                              `invoice_revision` varchar(250) NOT NULL,
                              `invoice_revision_tax` int(3) NOT NULL,
                              `invoice_revision_currency` int(3) NOT NULL,
                              `invoice_revision_notes` text NOT NULL,
                              `invoice_revision_term_condition` text NOT NULL,
                              PRIMARY KEY (`invoice_revision_id`)
				) ENGINE = MYISAM  ";
                    
                    $table[] = "sales_owner";
                    $sql[] = "(`sales_owner_id` int(11) NOT NULL auto_increment,
                              `sales_owner_name` varchar(250) NOT NULL,
                              `sales_owner_address1` varchar(500) NOT NULL,
                              `sales_owner_address2` varchar(500) NOT NULL,
                              `sales_owner_phone1` varchar(25) NOT NULL,
                              `sales_owner_phone2` varchar(25) NOT NULL,
                              `sales_owner_email` varchar(250) NOT NULL,
                              `sales_owner_city` varchar(100) NOT NULL,
                              `sales_owner_state` varchar(100) NOT NULL,
                              `sales_owner_country` varchar(100) NOT NULL,
                              `sales_owner_postal_code` varchar(100) NOT NULL,
                              `sales_owner_website` varchar(100) NOT NULL,
                              `sales_owner_reg_no` varchar(100) NOT NULL,
                              `sales_owner_gst_reg_no` varchar(100) NOT NULL,
                              `sales_owner_fax` varchar(50) NOT NULL,
                              PRIMARY KEY  (`sales_owner_id`)
				) ENGINE = MYISAM  ";


                    $table[] = "sales_payment";
                    $sql[] = "(
                              `payment_id` int(11) NOT NULL AUTO_INCREMENT,
                              `invoice_revision_id` int(11) NOT NULL,
                              `payment_amount` float NOT NULL,
                              `payment_method` int(11) NOT NULL,
                              `payment_date` datetime NOT NULL,
                              `payment_notes` varchar(500) NOT NULL,
                              `payment_receipt_no` varchar(100) NOT NULL,
                              PRIMARY KEY (`payment_id`)
				) ENGINE = MYISAM  ";

                    $table[] = "sales_payment_schedule";
                    $sql[] = "(
                              `payment_schedule_id` int(11) NOT NULL AUTO_INCREMENT,
                              `invoice_revision_id` int(11) NOT NULL,
                              `payment_schedule_paid` float NOT NULL,
                              `payment_schedule_paid_date` datetime NOT NULL,
                              `payment_schedule_notes` varchar(500) NOT NULL,
                              PRIMARY KEY (`payment_schedule_id`)
				) ENGINE = MYISAM  ";


                    $table[] = "sales_quotation";
                    $sql[] = "(
                              `quotation_id` int(11) NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `supplier_id` int(11) NOT NULL,
                              `customer_id` int(11) NOT NULL,
                              `address_id` int(11) NOT NULL,
                              `attention_id` int(11) NOT NULL,
                              `quotation_date` date NOT NULL,
                              `quotation_no` varchar(300) NOT NULL,
                              `quotation_sale_person` varchar(300) NOT NULL,
                              `quotation_sale_person_email` varchar(300) NOT NULL,
                              `quotation_sale_person_phone` varchar(300) NOT NULL,
                              `quotation_status` tinyint(1) NOT NULL,
                              `quotation_internal_notes` text NOT NULL,
                              `quotation_relation` tinyint(1) NOT NULL,
                              `quotation_template` int(1) NOT NULL,
                              PRIMARY KEY (`quotation_id`)
				) ENGINE = MYISAM  ";

                    $table[] = "sales_quotation_customer_mapping";
                    $sql[] = "(
                              `sales_quotation_customer_mapping_id` int(11) NOT NULL auto_increment,
                              `quotation_id` int(11) NOT NULL,
                              `contact_or_attention_id` int(11) NOT NULL,
                              `customer_type` tinyint(1) NOT NULL COMMENT '0: address, 1: attention',
                              PRIMARY KEY  (`sales_quotation_customer_mapping_id`)
				) ENGINE = MYISAM  ";

                    $table[] = "sales_quotation_item";
                    $sql[] = "(
                              `quotation_item_id` int(11) NOT NULL AUTO_INCREMENT,
                              `quotation_id` int(11) NOT NULL,
                              `quotation_revision_id` int(11) NOT NULL,
                              `quotation_item` varchar(300) NOT NULL,
                              `quotation_item_price` float NOT NULL,
                              `quotation_item_quantity` int(4) NOT NULL,
                              `quotation_item_discount` int(4) NOT NULL,
                              PRIMARY KEY (`quotation_item_id`)
				) ENGINE = MYISAM  ";


                    $table[] = "sales_quotation_revision";
                    $sql[] = "(
                              `quotation_revision_id` int(11) NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `quotation_id` int(11) NOT NULL,
                              `quotation_revision` varchar(200) NOT NULL,
                              `quotation_revision_tax` int(3) NOT NULL,
                              `quotation_revision_currency` int(3) NOT NULL,
                              `quotation_revision_notes` text NOT NULL,
                              `quotation_revision_term_condition_contents` text NOT NULL,
                              `quotation_revision_is_approve` tinyint(1) NOT NULL,
                              PRIMARY KEY (`quotation_revision_id`)
				) ENGINE = MYISAM  ";


                    //$table[] = "sales_receipt";
                    //$sql[] = "(
                      //        `receipt_id` int(11) NOT NULL AUTO_INCREMENT,
                        //      `payment_id` int(11) NOT NULL,
                          //    `receipt_no` varchar(100) NOT NULL,
                            //  PRIMARY KEY (`receipt_id`)
				//) ENGINE = MYISAM  ";


                    $table[] = "sales_tax";
                    $sql[] = "(
                              `tax_id` int(11) NOT NULL AUTO_INCREMENT,
                              `user_id` int(11) NOT NULL,
                              `tax_name` varchar(300) NOT NULL,
                              `tax_rate` varchar(250) NOT NULL,
                              `tax_default` tinyint(1) NOT NULL DEFAULT '0',
                              PRIMARY KEY (`tax_id`)
				) ENGINE = MYISAM  ";
                    
                    
                    $table[] = "quo_invc_history";
                    $sql[] = "(
                                `quo_invc_history_id` int(11) NOT NULL AUTO_INCREMENT,
                                `quo_invc_id` int(11) NOT NULL,
                                `quo_or_invc_history` int(11) NOT NULL,
                                `quo_invc_history_type` int(2) NOT NULL,
                                `quo_invc_history_update` varchar(300) NOT NULL,
                                `quo_invc_history_user` int(11) NOT NULL,
                                `quo_invc_history_date` datetime DEFAULT NULL,
                                PRIMARY KEY (`quo_invc_history_id`)
				) ENGINE = MYISAM  ";

                    
                    $table[] = "sales_template";
                    $sql[] = "(
                                `templ_id` int(11) NOT NULL AUTO_INCREMENT,
                                `user_id` int(11) NOT NULL,
                                `templ_name` varchar(250) NOT NULL,
                                `quo_invc_history_type` int(11) NOT NULL,
                                `templ_type` tinyint(1) NOT NULL,
                                PRIMARY KEY (`templ_id`)
				) ENGINE = MYISAM  ";
                    
                    $table[] = "sale_template_items";
                    $sql[] = "(
                                `item_temp_id` int(11) NOT NULL AUTO_INCREMENT,
                                `templ_id` int(11) NOT NULL,
                                `item_temp_item` varchar(250) NOT NULL,
                                `item_temp_quan` int(11) NOT NULL,
                                `item_temp_price` float NOT NULL,
                                `item_temp_discount` float NOT NULL,
                                `item_temp_amount` float NOT NULL,
                                PRIMARY KEY (`item_temp_id`)
				) ENGINE = MYISAM  ";

                    
                    $table[] = "sale_template_note";
                    $sql[] = "(
                                `note_temp_id` int(11) NOT NULL AUTO_INCREMENT,
                                `templ_id` int(11) NOT NULL,
                                `note_temp_content` varchar(250) NOT NULL,
                                PRIMARY KEY (`note_temp_id`)
				) ENGINE = MYISAM  ";

                    
                    $table[] = "note_temp_content";
                    $sql[] = "(
                                `term_temp_id` int(11) NOT NULL AUTO_INCREMENT,
                                `templ_id` int(11) NOT NULL,
                                `note_temp_content` varchar(300) NOT NULL,
                                PRIMARY KEY (`term_temp_id`)
				) ENGINE = MYISAM  ";
                    
                    $table[] = "sale_term_condition";
                    $sql[] = "(
                                `term_id` int(11) NOT NULL AUTO_INCREMENT,
                                `term_conttent` varchar(300) NOT NULL,
                                PRIMARY KEY (`term_id`)
				) ENGINE = MYISAM  ";
                    $table[] = "ale_template_term_condition";
                    $sql[] = "(
                                `term_temp_id` int(11) NOT NULL AUTO_INCREMENT,
                                `templ_id` int(11) NOT NULL,
                                `note_temp_content` varchar(300) NOT NULL,
                                PRIMARY KEY (`term_temp_id`)
				) ENGINE = MYISAM  ";

                    
                    if ($str_input == '')
                        return $define_tbl = array('tbl' => $table, 'sql' => $sql);
                    elseif ($str_input == 'tbl')
                        return $table;
                    elseif ($str_input == 'sql')
                        return $sql;
                }
                
            }
            

   ?>
