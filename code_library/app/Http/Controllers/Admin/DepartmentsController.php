@extends('layouts.app')

@section('content')
@include('users.partials.header', [
        'class' => 'col-lg-7'
    ])
@push('js')
<!-- Trigger the modal with a button -->
<style type="text/css">
 .hidden{display:none;}
</style>
<?php

// public function edit($id)
  // {
  //    $code = Code::find($id);
  //    $tags = DB::table('tags')
  //    ->join('code_tag', 'code_tag.tag_id', '=', 'tags.id')
  //    ->where(function($query)use ($id)      {
  //      $query->where('code_tag.code_id',$id);
  //    })->select('tags.id')
  //    ->get();
  //    $title   = trans('Edit');
  //    return view('codes.edit', compact('code', 'title','tags'));
// }

//if($r->parent){
  // $parents=explode(', ',$r->parent);
  // foreach ($parents as $parent) {
  //   if($parent!=''){
  //     DB::table('code_tag')->insert([
  //       ['code_id' => $id, 'tag_id' => $parent]
  //     ]);
  //   }
  // }
//}

$tgs=[];
  $tg='';
 foreach($tags as $tag){
   $tgs[]=$tag->id;
  }
?>
<script type="text/javascript">
$(document).ready(function(){
  $('.mn_tg').on('click',function () {
    $('.parent_id').val('');
    $("#jstree").jstree().deselect_all(true);
  })

  $('#jstree').jstree({
    "core" : {
      'data' :{!! get_dep($tgs) !!},
      "themes" : {
        "variant" : "large"
      }
    },
    'checkbox' : {
        'three_state' : false,
        'visible' : true,
        "keep_selected_style" : false
    },
    "plugins" : [ "wholerow","checkbox"]
  });

});

$('#jstree').on('changed.jstree',function(e,data){
    var i , j , r = [];
    var  name = [];
    for(i=0,j = data.selected.length;i < j;i++)
    {
        r.push(data.instance.get_node(data.selected[i]).id);
        name.push(data.instance.get_node(data.selected[i]).text);
    }
    $('.parent_id').val(r.join(', '));
    if(r.join(', ') != '')
    {
      $('#form_Delete_department').attr('action','{{ url('tags') }}/'+r.join(', '));
      $('#dep_name').text(name.join(', '));
      $('.showbtn_control').removeClass('hidden');
      $('.edit_dep').attr('href','{{ url('tags') }}/'+r.join(', ')+'/edit');
    }else{
      $('.showbtn_control').addClass('hidden');
    }
});
</script>
@endpush
  <div class="container-fluid mt--7">
    <div class="row">
      <div class="col-xl-5 order-xl-2 mb-5 mb-xl-0">
          <div class="card card-profile shadow">
              <div class="card-body pt-0 pt-md-4">
                  <div class="row">
                    <button type="button" class="btn btn-primary mn_tg showbtn_control mb-5 mt-4" style='margin:auto;' >Deselect</button>
                    <div id="jstree" class='col-12'></div>
                  </div>
              </div>
          </div>
      </div>
      <div class="col-xl-7 order-xl-1">
        <div class="card bg-secondary shadow">
          <div class="card-header bg-white border-0">
            <div class="row align-items-center">
                <h3 class="col-12 mb-0">{{ __('Edit Code') }}</h3>
            </div>
          </div>
          <div class="card-body">
            <form method="post" action="{{ route('addcode.update',$code->id) }}" enctype="multipart/form-data" autocomplete="off">
                @csrf
                @method('put')
                  <div class="pl-lg-4">
                    <input type="hidden" name="parent" id="" class='parent_id'>
                  </div>
                   <!-- $tags ?? '' }} -->
                  <div class="text-center">
                      <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                  </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
  <!-- /.box -->
  <?php
    function get_dep($select = null,$dep_hide = null) {
      $tags=App\Tag::selectRaw('name as text')
      ->selectRaw('id as id')
      ->selectRaw('parent as parent')->orderBy('name','asc')
      ->get(['text', 'parent', 'id']);
      $data=[];
      $obj=[];
      foreach ($tags as $key => $tag) {
        if($select !== null && in_array($tag->id,$select)){
          $obj['state']=[
            'opend'   =>true,
            'selected'=>true,
            'disabled' => false,
          ];
        }else{
          $obj['state']=[
          'opend'   =>false,
          'selected'=>false,
          // 'disabled' => true,
          ];
        }
        $tag->parent = $tag->parent ? $tag->parent :'#';
        $obj['id']=$tag->id;
        $obj['text']=$tag->text;
        $obj['parent']=$tag->parent;
        array_push($data,$obj);
      }
      return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
  ?>
