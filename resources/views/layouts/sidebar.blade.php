<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="navbar-nav">

            <li>
                <a class="nav-link" href="{{url('/dashboard')}}">
                    <i class="nav-icon icon-speedometer"></i> Dashboard
                </a>
            </li>
            @can('read-users','read-roles')
                <li class="nav-title">{{ __('Settings') }}</li>
            @endcan
            @can('read-users')
                <li class="nav-item">
                    <a class="nav-link " href="{{url('/users')}}">
                        <i class="nav-icon icon-people"></i> {{ __('Users') }}
                    </a>
                </li>
            @endcan
            @can('read-catalogs')
                <div class="nav-item dropdown" href="#">
                    <a class="nav-link nav-item dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="nav-icon icon-folder"></i> {{ __('Catalogs') }}</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01">
                        <a class="dropdown-item" href="{{url('/suppliers')}}"> <i class="nav-icon currentColor icon-people"></i> {{ __('Providers') }} </a>
                        @can('read-customers')
                            <a class="dropdown-item" href="{{url('/customers')}}">
                                <i class="nav-icon icon-user-following currentColor"></i> {{ __('Customers') }}
                            </a>
                        @endcan
                        @can('read-products-type')
                            <a class="dropdown-item" href="{{url('/products_type')}}">
                                <i class="nav-icon currentColor icon-bag"></i>Tipos de Productos
                            </a>
                        @endcan
                        @can('read-products')
                            <a class="dropdown-item" href="{{url('/products')}}">
                                <i class="nav-icon currentColor icon-bag"></i> Productos
                            </a>
                        @endcan
                        @can('read-sucursales')
                            <a class="dropdown-item" href="{{url('/sucursales')}}">
                                <i class="nav-icon currentColor icon-home"></i> Sucursales
                            </a>
                        @endcan
                    </div>
                </div>
            @endcan
            @can('read-warehouses')
                <div class="nav-item dropdown" href="#">
                    @can('read-warehouses')
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="nav-icon fas fa-industry"></i> Almacenes
                        </a>
                    @endcan
                    <div class="dropdown-menu" aria-labelledby="dropdown01">
                        <!--
                        <a class="dropdown-item" href="{{url('/stocks/create')}}">
                            <i class="nav-icon currentColor fas fa-warehouse"></i> Nuevo
                        </a>
                        -->
                        @can('read-integrate-production')
                          <a class="dropdown-item" href="{{url('/stocks/production')}}">
                              <i class="nav-icon currentColor fas fa-warehouse"></i> Integración de producto
                          </a>
                        @endcan
                        @can('read-final-product')
                          <a class="dropdown-item" href="{{url('/stocks')}}">
                              <i class="nav-icon currentColor fas fa-warehouse"></i> Producto Final
                          </a>
                        @endcan
                        @can('read-total-final-product')
                          <a class="dropdown-item" href="{{url('/stocks/granTotal')}}">
                              <i class="nav-icon currentColor fas fa-warehouse"></i>Total Producto Final
                          </a>
                        @endcan
                    </div>
                </div>
            @endcan

            @can('read-productions')
            <div class="nav-item dropdown" href="#">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="nav-icon fas fa-industry"></i> Produccion
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdown01">
                    @can('integrate-productions')
                    <a class="dropdown-item" href="{{url('/productions/create')}}">
                        <i class="nav-icon currentColor fas fa-warehouse"></i> Integración de producto
                    </a>
                    @endcan
                    @can('create-productions')
                    <a class="dropdown-item" href="{{url('/productions/create/final')}}">
                        <i class="nav-icon currentColor fas fa-warehouse"></i> Producto Final
                    </a>
                    @endcan
                </div>
            </div>
            @endcan

            @can('read-payments')
                <li class="nav-item">
                    <a class="nav-link" href="{{url('/payments')}}">
                        <i class="nav-icon icon-wallet"></i> {{ __('Payments') }}
                    </a>
                </li>
            @endcan
            @can('read-reports')
                <li class="nav-item">
                    <a class="nav-link" href="{{url('/reports')}}">
                        <i class="nav-icon icon-graph"></i> {{ __('Reports') }}
                    </a>
                </li>
            @endcan

            @can('read-orders')
                <div class="nav-item dropdown" href="#">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="nav-icon fas fa-industry"></i> Pedidos</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01">
                        @can('list-orders')
                        <a class="dropdown-item" href="{{url('/orders')}}">
                            <i class="nav-icon currentColor fas fa-drumstick-bite"></i> Lista
                        </a>
                        @endcan
                        @can('create-orders')
                        <a class="dropdown-item" href="{{url('/orders/create')}}">
                            <i class="nav-icon currentColor fas fa-drumstick-bite"></i> Nuevo
                        </a>
                        @endcan
                        @can('print-orders')
                        <a class="dropdown-item" href="{{url('/orders/print')}}">
                            <i class="nav-icon currentColor fas fa-print"></i> Impresión
                        </a>
                        @endcan
                    </div>
                </div>
            @endcan

            @can('read-office-payments')
                <li class="nav-item">
                    <a class="nav-link" href="{{url('/office_payments')}}">
                        <i class="nav-icon icon-wallet"></i> Gastos de oficina
                    </a>
                </li>
            @endcan

            @can('read-roles')
                <li class="nav-item dropdown">
                	<a class="nav-link  dropdown-toggle" href="#" data-toggle="dropdown">  <i class="nav-icon icon-key"></i> Roles/Permisos  </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{url('/roles')}}"> <i class="nav-icon currentColor icon-key"></i> {{ __('Roles') }} </a></li>
                	    <li><a class="dropdown-item" href="{{url('/permissions')}}"> <i class="nav-icon currentColor icon-key"></i> {{ __('Permissions') }}  </a></li>
                    </ul>
                </li>
            @endcan
        </ul>
    </nav>
    <sidebar></sidebar>
</div>
