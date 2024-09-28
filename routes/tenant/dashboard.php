<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function (\App\Main\Tenant\Domain\Models\Tenant $tenantInstance) {
    return ["Welcome Tenant (" . $tenantInstance->name . ") Dashboard Apis"];
});


Route::group(["prefix" => "auth"], function () {
    Route::post("/login", \App\Tenant\Admin\Actions\Auth\LoginAdminAction::class);
    Route::post("/forget-password", \App\Tenant\Admin\Actions\Auth\ForgetPasswordAction::class);
    Route::post("/reset-password", \App\Tenant\Admin\Actions\Auth\ResetPasswordAction::class);
});


Route::middleware(['auth:tenant-admin'])->group(function () {
    Route::post("/auth/logout", \App\Tenant\Admin\Actions\Auth\LogoutAdminAction::class);

    Route::group(["prefix" => "auth/profile"], function () {
        Route::get("/", function () {
            return auth()->user();
        });
        Route::put("/", \App\Tenant\Admin\Actions\Auth\UpdateProfileAction::class)->name("admins.update_profile");
        Route::put("/change-password", \App\Tenant\Admin\Actions\Auth\ChangePasswordAction::class)->name("admins.update_password");
    });


    //location
    Route::group(["prefix" => "location"], function () {
        //countries
        Route::group(["prefix" => "/countries"], function () {
            Route::get("/", \App\Tenant\Location\Actions\ListCountriesAction::class)->name("countries.index")->middleware('can:countries.index');
            Route::get("/{id}", \App\Tenant\Location\Actions\ShowCountryAction::class)->name("countries.show");
            Route::post("/", \App\Tenant\Location\Actions\CreateCountryAction::class)->name("countries.store")->middleware('can:countries.create');
            Route::put("/{id}/toggle-status", \App\Tenant\Location\Actions\ToggleCountryStatusAction::class)->name("countries.toggle_status")->middleware('can:countries.update');
            Route::put("/{id}", \App\Tenant\Location\Actions\UpdateCountryAction::class)->name("countries.update")->middleware('can:countries.update');
            Route::delete("/{id}", \App\Tenant\Location\Actions\DeleteCountryAction::class)->name("countries.destroy")->middleware('can:countries.delete');
        });

        //states
        Route::group(["prefix" => "/states"], function () {
            Route::get("/", \App\Tenant\Location\Actions\ListStatesAction::class)->name("states.index")->middleware('can:states.index');
            Route::get("/{id}", \App\Tenant\Location\Actions\ShowStateAction::class)->name("states.show");
            Route::post("/", \App\Tenant\Location\Actions\CreateStateAction::class)->name("states.store")->middleware('can:states.create');
            Route::put("/{id}", \App\Tenant\Location\Actions\UpdateStateAction::class)->name("states.update")->middleware('can:states.update');
            Route::put("/{id}/toggle-status", \App\Tenant\Location\Actions\ToggleStateStatusAction::class)->name("states.toggle_status")->middleware('can:states.update');
            Route::delete("/{id}", \App\Tenant\Location\Actions\DeleteStateAction::class)->name("states.destroy")->middleware('can:states.delete');
        });

        //cities
        Route::group(["prefix" => "/cities"], function () {
            Route::get("/", \App\Tenant\Location\Actions\ListCitiesAction::class)->name("cities.index")->middleware('can:cities.index');
            Route::get("/{id}", \App\Tenant\Location\Actions\ShowCityAction::class)->name("cities.show");
            Route::post("/", \App\Tenant\Location\Actions\CreateCityAction::class)->name("cities.store")->middleware('can:cities.create');
            Route::put("/{id}", \App\Tenant\Location\Actions\UpdateCityAction::class)->name("cities.update")->middleware('can:cities.update');
            Route::put("/{id}/toggle-status", \App\Tenant\Location\Actions\ToggleCityStatusAction::class)->name("cities.toggle_status")->middleware('can:cities.update');
            Route::delete("/{id}", \App\Tenant\Location\Actions\DeleteCityAction::class)->name("cities.destroy")->middleware('can:cities.delete');
        });
    });

    //brands
    Route::group(["prefix" => "brands"], function () {
        Route::get("/", \App\Tenant\Brand\Actions\ListBrandsAction::class)->name("brands.index")->middleware('can:brands.index');
        Route::get("/{id}", \App\Tenant\Brand\Actions\ShowBrandAction::class)->name("brands.show");
        Route::post("/", \App\Tenant\Brand\Actions\CreateBrandAction::class)->name("brands.store")->middleware('can:brands.create');
        Route::put("/{id}", \App\Tenant\Brand\Actions\UpdateBrandAction::class)->name("brands.update")->middleware('can:brands.update');
        Route::delete("/{id}", \App\Tenant\Brand\Actions\DeleteBrandAction::class)->name("brands.destroy")->middleware('can:brands.delete');
        Route::put("/{id}/toggle-status", \App\Tenant\Brand\Actions\ToggleBrandStatusAction::class)->name("brands.toggle_status")->middleware('can:brands.update');
    });

    //categories
    Route::group(["prefix" => "categories"], function () {
        Route::get("/", \App\Tenant\Category\Actions\ListCategoriesAction::class)->name("categories.index");
        Route::get("/{id}", \App\Tenant\Category\Actions\ShowCategoryAction::class)->name("categories.show");
        Route::post("/", \App\Tenant\Category\Actions\CreateCategoryAction::class)->name("categories.store");
        Route::put("/{id}", \App\Tenant\Category\Actions\UpdateCategoryAction::class)->name("categories.update");
        Route::delete("/{id}", \App\Tenant\Category\Actions\DeleteCategoryAction::class)->name("categories.destroy");
        Route::put("/{id}/toggle-status", \App\Tenant\Category\Actions\ToggleCategoryStatusAction::class)->name("categories.toggle_status");
    });

    //products
    Route::group(["prefix" => "products"], function () {
        Route::get("/", \App\Tenant\Product\Actions\ListProductsAction::class)->name("products.index");
        Route::get("/{id}", \App\Tenant\Product\Actions\ShowProductAction::class)->name("products.show");
        Route::post("/", \App\Tenant\Product\Actions\CreateProductAction::class)->name("products.create");
        Route::put("/{id}", \App\Tenant\Product\Actions\UpdateProductAction::class)->name("products.update");
        Route::put("/{id}/toggle-status", \App\Tenant\Product\Actions\ToggleProductStatusAction::class)->name("products.toggle_status");
        Route::delete("/{id}", \App\Tenant\Product\Actions\DeleteProductAction::class)->name("products.destroy");
    });

    //offers
    Route::group(["prefix" => "offers"], function () {
        Route::get("/", \App\Tenant\Offer\Actions\ListOffersAction::class)->name("offers.index")->middleware('can:offers.index');
        Route::get("/{id}", \App\Tenant\Offer\Actions\ShowOfferAction::class)->name("offers.show");
        Route::post("/", \App\Tenant\Offer\Actions\CreateOfferAction::class)->name("offers.Offer")->middleware('can:offers.create');
        Route::delete("/{id}", \App\Tenant\Offer\Actions\DeleteOfferAction::class)->name("offers.destroy");
        Route::put("/{id}", \App\Tenant\Offer\Actions\UpdateOfferAction::class)->name("offers.create")->middleware('can:offers.update');
        Route::put("/{id}/toggle-status", \App\Tenant\Offer\Actions\ToggleOfferStatusAction::class)->name("offers.toggle_status")->middleware('can:offers.update');
    });

    //stores
    Route::group(["prefix" => "stores"], function () {
        Route::get("/", \App\Tenant\Store\Actions\ListStoresAction::class)->name("stores.index");
        Route::get("/list-temp-store", \App\Tenant\Store\Actions\ListStoresTempAction::class)->name("stores.temp-index");
        Route::get("/export-to-excel", \App\Tenant\Store\Actions\ExportStoresToExcelAction::class)->name("stores.export");
        Route::post("/featured", \App\Tenant\Store\Actions\SetFeaturedStoresAction::class)->name("stores.featured");
        Route::get("/temp-stores/{id}", \App\Tenant\Store\Actions\ShowStoreTempAction::class)->name("stores.get-temp");
        Route::post("/temp-stores", \App\Tenant\Store\Actions\TempStoreActionAction::class)->name("stores.update-temp");
        Route::get("/{id}", \App\Tenant\Store\Actions\ShowStoreAction::class)->name("stores.show");

        Route::post("/", \App\Tenant\Store\Actions\CreateStoreAction::class)->name("stores.store");
        // Route::put("/{id}", [\App\Infrastructure\Http\Controllers\Seller\StoreController::class, 'update'])->name("stores.update");
        Route::put("/{id}/process-request", \App\Tenant\Store\Actions\ProcessJoinStoresAction::class)->name("stores.process_request");
        Route::put("/{id}/toggle-status", \App\Tenant\Store\Actions\ToggleStoreStatusAction::class)->name("stores.toggle_status");
    });

    //orders
    Route::group(["prefix" => "orders"], function () {
        Route::get("/", \App\Tenant\Order\Actions\ListOrdersAction::class)->name("orders.index")->middleware('can:orders.index');
        Route::get("/export-to-excel", \App\Tenant\Order\Actions\ExportOrdersToExcelAction::class)->name("orders.export")->middleware('can:orders.index');
        Route::put("/{id}/change-status", \App\Tenant\Order\Actions\UpdateOrderStatusAction::class)->name("orders.change_status")->middleware('can:orders.update');
        Route::put("/change-statuses", \App\Tenant\Order\Actions\UpdateOrdersStatusAction::class)->name("orders.update_status")->middleware('can:orders.update');
        Route::get("/{id}", \App\Tenant\Order\Actions\ShowOrderAction::class)->name("orders.show");
        Route::post("/", \App\Tenant\Order\Actions\CreateOrderAction::class)->name("orders.store")->middleware('can:orders.create');
        Route::put("/{id}", \App\Tenant\Order\Actions\UpdateOrderAction::class)->name("orders.update")->middleware('can:orders.update');
        Route::put("/service/{id}", \App\Tenant\Order\Actions\UpdateServiceOrderAction::class)->name("serice_orders.update")->middleware('can:orders.update');
        Route::delete("/{id}", \App\Tenant\Order\Actions\DeleteOrderAction::class)->name("orders.destroy")->middleware('can:orders.delete');
        //Route::post("/{id}/refund", \App\Tenant\Refund\Actions\RefundOrderAction::class)->name("orders.refund")->middleware('can:refunds.create');
    });


    Route::group(["prefix" => "settings"], function () {
        Route::get("/", \App\Tenant\AppContent\Actions\Setting\GetSettingsAction::class)->name("settings.index")->middleware('can:settings.index');
        Route::post("/", \App\Tenant\AppContent\Actions\Setting\UpdateSettingsAction::class)->name("settings.store")->middleware('can:settings.index');
    });

    Route::group(["prefix" => "pages"], function () {
        Route::get("/", \App\Tenant\AppContent\Actions\Page\ListPagesAction::class)->name("pages.index")->middleware('can:pages.index');
        Route::get("/{slug}", \App\Tenant\AppContent\Actions\Page\GetPageAction::class)->name("pages.index");
        Route::put("/{slug}", \App\Tenant\AppContent\Actions\Page\UpdatePageAction::class)->name("pages.update")->middleware('can:pages.index');
    });

    Route::group(["prefix" => "admins"], function () {
        Route::get("/", \App\Tenant\Admin\Actions\Admin\ListAdminsAction::class)->name("admins.index")->middleware('can:admins.index');
        Route::get("/export-to-excel", \App\Tenant\Admin\Actions\Admin\ExportAdminsToExcelAction::class)->name("admins.export")->middleware('can:admins.index');
        Route::get("/{id}", \App\Tenant\Admin\Actions\Admin\GetAdminAction::class)->name("admins.show");
        Route::post("/", \App\Tenant\Admin\Actions\Admin\CreateAdminAction::class)->name("admins.store")->middleware('can:admins.create');
        Route::put("/{id}", \App\Tenant\Admin\Actions\Admin\UpdateAdminAction::class)->name("admins.update")->middleware('can:admins.update');
        Route::delete("/{id}", \App\Tenant\Admin\Actions\Admin\DeleteAdminAction::class)->name("admins.destroy")->middleware('can:admins.delete');
        Route::put("/{id}/toggle-status", \App\Tenant\Admin\Actions\Admin\ToggleAdminStatusAction::class)->name("admins.toggle_status")->middleware('can:admins.update');
        // Route::post("/{id}/permissions", \App\Tenant\Admin\Actions\Admin\AssignPermissionsToAdminAction::class)->name("admins.assignPermissions");

    });

});
