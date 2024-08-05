@php
    use Illuminate\Support\Facades\Auth;
    $role = Auth::user()->role;
    $status = Auth::user()->status;
    $can = Auth::user()->can;
@endphp
<div class="sidebar-wrapper" data-simplebar="true">
    <a href="/">
        <div class="sidebar-header">
            <div>
                <img src="{{ asset('backend_assets') }}/images/siostore_logo.png" class="logo-icon" alt="logo icon">
            </div>
            <div>
                <h4 class="logo-text">SioStore</h4>
            </div>
            <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
            </div>
        </div>
    </a>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li class="menu-label">Profile Menu</li>
        <li>
            <a href="{{ route('dashboard') }}" aria-expanded="false">
                <div class="parent-icon"><i class="bx bx-chart"></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        <li>
            <a href="{{ route($role . '-profile') }}" aria-expanded="false">
                <div class="parent-icon"><i class="bx bx-user-circle"></i>
                </div>
                <div class="menu-title">Profile</div>
            </a>
        </li>
        @if ($role == 'vendor')
            <li>
                <a href="{{ route('store.showVendor', Auth::user()->vendor_shop->vendor_id) }}" aria-expanded="false">
                    <div class="parent-icon"><i class="lni lni-arrow-left"></i>
                    </div>
                    <div class="menu-title">Visit store</div>
                </a>
            </li>
            <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-graph'></i>
                    </div>
                    <div class="menu-title">Orders</div>
                </a>
                <ul>
                    <li> <a href="{{ route($role . '-vendorOrders') }}"><i class="bx bx-right-arrow-alt"></i>Show
                            All</a>
                    </li>
                </ul>
            </li>
        @else
            <li>
                <a href="/" aria-expanded="false">
                    <div class="parent-icon"><i class="lni lni-arrow-left"></i>
                    </div>
                    <div class="menu-title">Visit store</div>
                </a>
            </li>
        @endif

        <li>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <a href="{{ route('logout') }}" aria-expanded="false"
                    onclick="event.preventDefault(); this.closest
                ('form').submit();">
                    <div class="parent-icon"><i class="bx bx-log-out-circle"></i>
                    </div>
                    <div class="menu-title">Logout</div>
                </a>
            </form>

        </li>


        <li class="menu-label">Personal Shopping Menu</li>
        <li>
            <a class="has-arrow" style="cursor: pointer">
                <div class="parent-icon"><i class='lni lni-map'></i>
                </div>
                <div class="menu-title">Shipping Addresses</div>
            </a>
            <ul>
                <li> <a href="{{ route('address.index') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                </li>
                <li> <a href="{{ route('address.create') }}"><i class="bx bx-right-arrow-alt"></i>Create New</a>
                </li>
            </ul>

        </li>
        <li>
            <a class="has-arrow" style="cursor: pointer">
                <div class="parent-icon"><i class='lni lni-heart'></i>
                </div>
                <div class="menu-title">Wishlist</div>
            </a>
            <ul>
                <li> <a href="{{ route('wishlist.index') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                </li>
            </ul>

        </li>
        <li>
            <a class="has-arrow" style="cursor: pointer">
                <div class="parent-icon"><i class='lni lni-checkmark-circle'></i>
                </div>
                <div class="menu-title">Orders</div>
            </a>
            <ul>
                <li> <a href="{{ route('user-orders.index') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                </li>
            </ul>

        </li>
        {{-- <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='bx bx-bell'></i>
                    </div>
                    <div class="menu-title">Notifications</div>
                </a>
                <ul>
                    <li> <a href="#"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                    </li>
                </ul>
            </li> --}}

        @if ($role === 'admin')
            <li class="menu-label">Admin Menu</li>

            @if (isset($can) && ($can == 'all' || $can == 'kyc'))
                <li>
                    <a href="{{ route('admin-vendor-list') }}" style="cursor: pointer">
                        <div class="parent-icon"><i class='lni lni-world'></i>
                        </div>
                        <div class="menu-title">Vendors</div>
                    </a>

                </li>
                <li>
                    <a href="{{ route('admin-user-list-page') }}" style="cursor: pointer">
                        <div class="parent-icon"><i class='lni lni-user'></i>
                        </div>
                        <div class="menu-title">Users</div>
                    </a>

                </li>
                <li>
                    <a href="{{ route('add-currency') }}" aria-expanded="false">
                        <div class="parent-icon"><i class="bx bx-user-circle"></i>
                        </div>
                        <div class="menu-title">Currency</div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin-country-list') }}" aria-expanded="false">
                        <div class="parent-icon"><i class="bx bx-flag"></i>
                        </div>
                        <div class="menu-title">Country</div>
                    </a>
                </li>
            @endif
            @if (isset($can) && ($can == 'all' || $can == 'accounts'))
                <li>
                    <a href="{{ route('admin-allOrders') }}" style="cursor: pointer">
                        <div class="parent-icon"><i class='lni lni-plane'></i>
                        </div>
                        <div class="menu-title">All Orders</div>
                    </a>

                </li>
                <li>
                    <a href="{{ route('admin-all-payments') }}" style="cursor: pointer">
                        <div class="parent-icon"><i class='lni lni-coin'></i>
                        </div>
                        <div class="menu-title">Retail Accounting</div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('payout-list') }}" style="cursor: pointer">
                        <div class="parent-icon"><i class='lni lni-plus'></i>
                        </div>
                        <div class="menu-title">Vendor Payouts</div>
                    </a>

                </li>
                <li class="menu-label">Products Menu</li>

                <li>
                    <a class="has-arrow" style="cursor: pointer">
                        <div class="parent-icon"><i class='lni lni-checkmark-circle'></i>
                        </div>
                        <div class="menu-title">Brands</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('brand') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                        </li>
                        <li> <a href="{{ route('brand-add') }}"><i class="bx bx-right-arrow-alt"></i>Add Brand</a>
                        </li>
                    </ul>

                </li>
                <li>
                    <a class="has-arrow" style="cursor: pointer">
                        <div class="parent-icon"><i class='lni lni-folder'></i>
                        </div>
                        <div class="menu-title">Categories</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('category') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                        </li>
                        <li> <a href="{{ route('category-add') }}"><i class="bx bx-right-arrow-alt"></i>Add
                                Category</a>
                        </li>
                    </ul>
                </li>
            @endif
            <li>
                <a href="{{ route('admin-product-list-page') }}" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-gift'></i>
                    </div>
                    <div class="menu-title">Products</div>
                </a>

            </li>

            <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-network'></i>
                    </div>
                    <div class="menu-title">Announcements</div>
                </a>
                <ul>
                    <li> <a href="{{ route('announce.index') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                    </li>
                    <li> <a href="{{ route('announce.create') }}"><i class="bx bx-right-arrow-alt"></i>Add
                            Announcement</a>
                    </li>
                </ul>
            </li>
        @endif

        @if ($status && $role == 'vendor')
            <li class="menu-label">Products Menu</li>
            {{-- <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-dinner'></i>
                    </div>
                    <div class="menu-title">Sub Categories</div>
                </a>
                <ul>
                    <li> <a href="{{ route('sub-category') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                    </li>
                    <li> <a href="{{ route('sub-category-add') }}"><i class="bx bx-right-arrow-alt"></i>Add Sub
                            Category</a>
                    </li>
                </ul>
            </li> --}}
            <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-cart'></i>
                    </div>
                    <div class="menu-title">POS</div>
                </a>
                <ul>
                    <li> <a href="{{ route('walk-in-order.index') }}"><i class="bx bx-right-arrow-alt"></i>Show All Sales</a>
                    </li>
                    <li> <a href="{{ route('walk-in-order.create') }}"><i class="bx bx-right-arrow-alt"></i>New Sale</a>
                    </li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-graph'></i>
                    </div>
                    <div class="menu-title">Products</div>
                </a>
                <ul>
                    <li> <a href="{{ route($role . '-product') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                    </li>
                    <li> <a href="{{ route('vendor-product-add') }}"><i class="bx bx-right-arrow-alt"></i>Add
                            Product</a>
                    </li>
                </ul>
            </li>
            {{-- <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-wallet'></i>
                    </div>
                    <div class="menu-title">Coupons</div>
                </a>
                <ul>
                    <li> <a href="{{ route($role . '-coupon') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                    </li>
                    <li> <a href="{{ route('vendor-coupon-add') }}"><i class="bx bx-right-arrow-alt"></i>Add
                            Coupon</a>
                    </li>
                </ul>
            </li> --}}
        @endif
        @if ($status && $role != 'admin' && isset($can) && $can == 'all')
            <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-checkmark-circle'></i>
                    </div>
                    <div class="menu-title">Brands</div>
                </a>
                <ul>
                    <li> <a href="{{ route('brand') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                    </li>
                    <li> <a href="{{ route('brand-add') }}"><i class="bx bx-right-arrow-alt"></i>Add Brand</a>
                    </li>
                </ul>

            </li>
            <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-folder'></i>
                    </div>
                    <div class="menu-title">Categories</div>
                </a>
                <ul>
                    <li> <a href="{{ route('category') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                    </li>
                    <li> <a href="{{ route('category-add') }}"><i class="bx bx-right-arrow-alt"></i>Add Category</a>
                    </li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-dinner'></i>
                    </div>
                    <div class="menu-title">Sub Categories</div>
                </a>
                <ul>
                    <li> <a href="{{ route('sub-category') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                    </li>
                    <li> <a href="{{ route('sub-category-add') }}"><i class="bx bx-right-arrow-alt"></i>Add Sub
                            Category</a>
                    </li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-graph'></i>
                    </div>
                    <div class="menu-title">Products</div>
                </a>
                <ul>
                    <li> <a href="{{ route($role . '-product') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                    </li>
                    <li> <a href="{{ route('vendor-product-add') }}"><i class="bx bx-right-arrow-alt"></i>Add
                            Product</a>
                    </li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" style="cursor: pointer">
                    <div class="parent-icon"><i class='lni lni-wallet'></i>
                    </div>
                    <div class="menu-title">Coupons</div>
                </a>
                <ul>
                    <li> <a href="{{ route($role . '-coupon') }}"><i class="bx bx-right-arrow-alt"></i>Show All</a>
                    </li>
                    <li> <a href="{{ route('vendor-coupon-add') }}"><i class="bx bx-right-arrow-alt"></i>Add
                            Coupon</a>
                    </li>
                </ul>
            </li>
        @endif
    </ul>
    <!--end navigation-->
</div>
