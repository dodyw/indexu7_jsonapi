<?php
  include "../global.php";
  
//  api/?json=recent&count=10&apikey=3949
//  api/?json=featured&count=10&apikey=3949
//  api/?json=categorylist&parent_id=29&count=10&apikey=3949
//  api/?json=listing&category=29&includechild=1&count=10&apikey=3949
//  api/?json=search&kw=gundam&count=10&apikey=3949
  
  if ($lep->input['apikey']!='i394jf' && $lep->input['apikey']!='') {
    print("invalid key");
    exit;
  }

  if ($lep->input['count']) {
    $nrows = $lep->input['count'];
  }
  else {
    $nrows = 10;
  }

  if ($lep->input['kw']) {
    $keyword = $lep->input['kw'];
  }
  else {
    $keyword = 10;
  }

  if ($lep->input['category']) {
    $category_id = $lep->input['category'];
  }
  else {
    $category_id = '0';
  }  

  /********************************************************/
  
  switch ($lep->input['json']) {

    case "recent":
      $query = "select *
                from lep_resource
                where status = 1 order by created_at desc 
                limit $nrows";
      $rs = $lep->db->Execute($query);
      $resources = $rs->GetRows();

      foreach ($resources as $k => $v) {
        $query = "select *
                  from lep_category
                  where status = 1 and category_id = '{$v['category_id']}'"; 
        $rs = $lep->db->Execute($query);
        $categ = $rs->FetchRow();

        $x = $v;
        $x['categ_name'] = $categ['title'];  
        $x['categ_path'] = $categ['path'];  
        $x['categ_path'] = $categ['path_url'];  

        $data[] = $x;
      }

    case "featured":
      $time_now = time();
      $query = "select *
                from lep_resource
                where status = 1 and featured = 1 and featured_expired > '$time_now'
                order by created_at desc 
                limit $nrows";
      $rs = $lep->db->Execute($query);
      $resources = $rs->GetRows();

      foreach ($resources as $k => $v) {
        $query = "select *
                  from lep_category
                  where status = 1 and category_id = '{$v['category_id']}'"; 
        $rs = $lep->db->Execute($query);
        $categ = $rs->FetchRow();

        $x = $v;
        $x['categ_name'] = $categ['title'];  
        $x['categ_path'] = $categ['path'];  
        $x['categ_path'] = $categ['path_url'];  

        $data[] = $x;
      }

    case "listing":

      if ($lep->input['includechild']) {
        $categ_list = cat_get_children($category_id);
      }
      else {
        $categ_list = $category_id;        
      }

      $query = "select *
                from lep_resource
                where status = 1 and category_id in ($categ_list) order by created_at desc 
                limit $nrows";
      $rs = $lep->db->Execute($query);
      $resources = $rs->GetRows();

      foreach ($resources as $k => $v) {
        $query = "select *
                  from lep_category
                  where status = 1 and category_id = '{$v['category_id']}'"; 
        $rs = $lep->db->Execute($query);
        $categ = $rs->FetchRow();

        $x = $v;
        $x['categ_name'] = $categ['title'];  
        $x['categ_path'] = $categ['path'];  
        $x['categ_path'] = $categ['path_url'];  

        $data[] = $x;
      }
    default:
  }  


  print json_encode($data);
?>