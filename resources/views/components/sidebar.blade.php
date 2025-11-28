<div class="vertical-menu">


    <!-- LOGO -->
    <div class="navbar-brand-box">
       <a href="{{url('admin/dashboard')}}" class="logo logo-dark">
           <span class="logo-sm">
               <img src="{{url('assets/images/BYTE_LOGO.webp')}}" alt="" height="26">
           </span>
           <span class="logo-lg">
               <img src="{{url('assets/images/BYTE_LOGO.webp')}}" alt="" height="26"> <span class="logo-txt"></span>
           </span>
       </a>

       <a href="{{url('admin/dashboard')}}" class="logo logo-light">
           <span class="logo-sm">
               <img src="{{url('assets/images/BYTE_LOGO.webp')}}" alt="" height="26">
           </span>
           <span class="logo-lg">
               <img src="{{url('assets/images/BYTE_LOGO.webp')}}" alt="" height="26"> <span class="logo-txt"></span>
           </span>
       </a>
   </div>

   <button type="button" class="btn btn-sm px-3 font-size-16 header-item vertical-menu-btn">
       <i class="fa fa-fw fa-bars"></i>
   </button>

   <div data-simplebar class="sidebar-menu-scroll">

       <!--- Sidemenu -->
       <div id="sidebar-menu">
           <!-- Left Menu Start -->
           <ul class="metismenu list-unstyled" id="side-menu">
               <li class="menu-title" data-key="t-menu">Menu</li>

               <li>
                   <a href="{{url('admin/dashboard')}}">
                       <i class="bx bx-home-circle nav-icon"></i>
                       <span class="menu-item" data-key="t-dashboard">Dashboard</span>
                   </a>
               </li>
               <li>
                <a href="{{url('admin/orders')}}">
                    <i class="bx bx-home-circle nav-icon"></i>
                    <span class="menu-item" data-key="t-orders">Orders</span>
                </a>
            </li>

               <li class="menu-title" data-key="t-applications">Settings</li>

               <li>
                   <a href="{{url('admin/banners')}}">
                       <i class="bx bx-calendar-alt nav-icon"></i>
                       <span class="menu-item" data-key="t-calendar">Banners</span>
                   </a>
               </li>

               <li>
                   <a href="{{url('admin/users')}}">
                       <i class="bx bx-chat nav-icon"></i>
                       <span class="menu-item" data-key="t-chat">Users</span>

                   </a>
               </li>

               <li>
                <a href="{{url('admin/categories')}}">
                    <i class="bx bx-calendar-alt nav-icon"></i>
                    <span class="menu-item" data-key="t-calendar">Category</span>
                </a>
            </li>
            <li>
                <a href="{{url('admin/subcategories')}}">
                    <i class="bx bx-chat nav-icon"></i>
                    <span class="menu-item" data-key="t-chat">Sub Category</span>
                    {{-- <span class="badge rounded-pill text-danger bg-danger-subtle" da   ta-key="t-hot">Hot</span> --}}
                </a>
            </li>
            <li>
                <a href="{{url('admin/brands')}}">
                    <i class="bx bxl-trello nav-icon"></i>
                    <span class="menu-item" data-key="t-kanban">Brand</span>
                </a>
            </li>

            <li>
                <a href="{{url('admin/blogs')}}">
                    <i class="bx bxl-blogger nav-icon"></i>
                    <span class="menu-item" data-key="t-kanban">Blogs</span>
                </a>
            </li>

            <li>
                <a href="{{url('admin/currencies')}}">
                    <i class="bx bx-dollar nav-icon"></i>
                    <span class="menu-item" data-key="t-kanban">Currency</span>
                </a>
            </li>

                <li class="menu-title" data-key="t-applications">Product Management</li>
                <li>
                    <a href="{{url('admin/products')}}">
                        <i class="bx bx-calendar-alt nav-icon"></i>
                        <span class="menu-item" data-key="t-calendar">Products</span>
                    </a>
                </li>
                <li>
                    <a href="{{url('admin/products/rfq')}}">
                        <i class="bx bx-file-find nav-icon"></i>
                        <span class="menu-item" data-key="t-calendar">RFQ</span>
                    </a>
                </li>
                <li>
                    <a href="{{url('admin/products/bulk-import')}}">
                        <i class="bx bx-upload nav-icon"></i>
                        <span class="menu-item" data-key="t-calendar">Product Bulk Import</span>
                    </a>
                </li>
                <li>

               {{-- <li>
                   <a href="{{url('admin/attributes')}}">
                       <i class="bx bx-calendar-alt nav-icon"></i>
                       <span class="menu-item" data-key="t-calendar">Attributes</span>
                   </a>
               </li>
               <li>
                <a href="{{url('admin/product_images')}}">
                    <i class="bx bx-calendar-alt nav-icon"></i>
                    <span class="menu-item" data-key="t-calendar">Images</span>
                </a>
            </li>
            <li>
                <a href="{{url('admin/product-documents')}}">
                    <i class="bx bx-calendar-alt nav-icon"></i>
                    <span class="menu-item" data-key="t-calendar">Documents</span>
                </a>
            </li> --}}



           </ul>
       </div>

   </div>
</div>
