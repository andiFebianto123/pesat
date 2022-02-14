@extends(backpack_view('blank'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('backpack::crud.list') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
  <div class="container-fluid">
    <h2>
      <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
      <small id="datatable_info_stack">{!! $crud->getSubheading() ?? '' !!}</small>
    </h2>
  </div>
@endsection

@section('content')
  <!-- Default box -->
  @if (isset($crud->viewBeforeContent) && is_array($crud->viewBeforeContent))
          @foreach ($crud->viewBeforeContent as $name)
            @include($name)
          @endforeach
  @endif
  @if(isset($crud->quickReport) && !isset($crud->requestQuickReport))

  @else
  @if(isset($crud->typeReport))
  <div class="row">
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link {{$crud->typeReport == 'annual' || $crud->typeReport == 'detail' ? 'active' : ''}}" aria-current="page" href="{{url($crud->routeAnnual)}}">{{$crud->entityNameAnnual}}</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{$crud->typeReport == 'designer' ? 'active' : ''}}" href="{{url($crud->routeDesigner)}}">Report Designer</a>
    </li>
  </ul>
  </div>
  @endif
  <div class="row {{isset($crud->typeReport) ? 'card' : ''}}">
    <div class="overlay"></div>
    @if(isset($crud->typeReport))

    <!-- CUSTOM CARD HEADER REPORT ANNUAL -->
    <div class="card-header">
          {{$crud->entityName}}
    </div>
    @endif
    <!-- THE ACTUAL CONTENT -->
    <div class="{{ $crud->getListContentClass() }}">
        @if(isset($crud->typeReport) && $crud->typeReport == "designer")
        <div class="row col-md-12 mt-3">
            <span class="col-sm-12 label-toggle"> Toggle Column Visibilty : </span>
        </div> 
        <div class="row mt-2">
          <div class="col-md-12">
              @foreach ($crud->columns() as $index => $column)
                    <div class="toggle-btn btn btn-primary mt-1 active" data-column="{{$loop->index}}">{!! $column['label'] !!}</div>
              @endforeach
          </div> 
        </div>
        @endif
        <div class="row mb-0">
          <div class="col-sm-6" {{isset($crud->typeReport) ? 'style=padding:1em;' : ''  }}>
            @if ( $crud->buttons()->where('stack', 'top')->count() ||  $crud->exportButtons())
              <div class="d-print-none {{ $crud->hasAccess('create')?'with-border':'' }}">

                @include('crud::inc.button_stack', ['stack' => 'top'])

              </div>
            @endif
          </div>
          <div class="col-sm-6" {{isset($crud->typeReport) ? 'style=padding:1em;' : ''  }}>
            <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none"></div>
          </div>
        </div>

        {{-- Backpack List Filters --}}
        @if ($crud->filtersEnabled())
          @include('crud::inc.filters_navbar')
        @endif

        <table id="crudTable" class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2" cellspacing="0">
            <thead>
              <tr>
                {{-- Table columns --}}
                @foreach ($crud->columns() as $column)
                  <th
                    data-orderable="{{ var_export($column['orderable'], true) }}"
                    data-priority="{{ $column['priority'] }}"
                     {{--

                        data-visible-in-table => if developer forced field in table with 'visibleInTable => true'
                        data-visible => regular visibility of the field
                        data-can-be-visible-in-table => prevents the column to be loaded into the table (export-only)
                        data-visible-in-modal => if column apears on responsive modal
                        data-visible-in-export => if this field is exportable
                        data-force-export => force export even if field are hidden

                    --}}

                    {{-- If it is an export field only, we are done. --}}
                    @if(isset($column['exportOnlyField']) && $column['exportOnlyField'] === true)
                      data-visible="false"
                      data-visible-in-table="false"
                      data-can-be-visible-in-table="false"
                      data-visible-in-modal="false"
                      data-visible-in-export="true"
                      data-force-export="true"
                    @else
                      data-visible-in-table="{{var_export($column['visibleInTable'] ?? false)}}"
                      data-visible="{{var_export($column['visibleInTable'] ?? true)}}"
                      data-can-be-visible-in-table="true"
                      data-visible-in-modal="{{var_export($column['visibleInModal'] ?? true)}}"
                      @if(isset($column['visibleInExport']))                     
                         @if($column['visibleInExport'] === false)
                           data-visible-in-export="false"   
                           data-force-export="false"    
                         @else    
                           data-visible-in-export="true"    
                           data-force-export="true"   
                         @endif   
                       @else    
                         data-visible-in-export="true"    
                         data-force-export="false"    
                       @endif
                    @endif
                  >
                    {!! $column['label'] !!}
                  </th>
                @endforeach

                @if ( $crud->buttons()->where('stack', 'line')->count() )
                  <th data-orderable="false" 
                      data-priority="{{ $crud->getActionsColumnPriority() }}" 
                      data-visible-in-export="false"
                      >{{ trans('backpack::crud.actions') }}</th>
                @endif
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                {{-- Table columns --}}
                @foreach ($crud->columns() as $column)
                  <th>{!! $column['label'] !!}</th>
                @endforeach

                @if ( $crud->buttons()->where('stack', 'line')->count() )
                  <th>{{ trans('backpack::crud.actions') }}</th>
                @endif
              </tr>
            </tfoot>
          </table>

          @if ( $crud->buttons()->where('stack', 'bottom')->count() )
          <div id="bottom_buttons" class="d-print-none text-center text-sm-left">
            @include('crud::inc.button_stack', ['stack' => 'bottom'])

            <div id="datatable_button_stack" class="float-right text-right hidden-xs"></div>
          </div>
          @endif
    </div>
      @if (isset($crud->viewAfterContent) && is_array($crud->viewAfterContent))
          @foreach ($crud->viewAfterContent as $name)
            @include($name)
          @endforeach
        @endif
  </div>
  @endif

@endsection

@section('after_styles')
  <!-- DATA TABLES -->
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.3.0/css/fixedColumns.bootstrap.min.css">

  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/crud.css').'?v='.config('backpack.base.cachebusting_string') }}">
  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/form.css').'?v='.config('backpack.base.cachebusting_string') }}">
  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/list.css').'?v='.config('backpack.base.cachebusting_string') }}">

  <!-- CRUD LIST CONTENT - crud_list_styles stack -->
  @stack('crud_list_styles')
  @if(isset($crud->typeReport) || isset($crud->quickReport))
  <style>
      
      .card-header{
        background-color: #b5c7e0;
        font-size: 20px;
        font-weight:bold;
      }

      .label-toggle{
        font-size: 16px;
        font-weight:bold;
      }

      .toggle-btn{
        cursor:pointer;
      }

      .toggle-btn.not-active{
        background-color: #7C69EF;
      }

  </style>
  @endif
@endsection

@section('after_scripts')
  @include('crud::inc.datatables_logic')
  <script src="{{ asset('packages/backpack/crud/js/crud.js').'?v='.config('backpack.base.cachebusting_string') }}"></script>
  <script src="{{ asset('packages/backpack/crud/js/form.js').'?v='.config('backpack.base.cachebusting_string') }}"></script>
  <script src="{{ asset('packages/backpack/crud/js/list.js').'?v='.config('backpack.base.cachebusting_string') }}"></script>

  <script>
      $(document).ready(function() {
        var table = $('#example').DataTable( {
            scrollY:        "300px",
            scrollX:        true,
            scrollCollapse: true,
            paging:         false,
            fixedColumns:   {
                leftColumns: 1,
                rightColumns: 1
            }
        } );
      } );
  </script>

  <!-- CRUD LIST CONTENT - crud_list_scripts stack -->
  @stack('crud_list_scripts')
  
@endsection
