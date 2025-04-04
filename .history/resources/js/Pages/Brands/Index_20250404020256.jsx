import { Head, Link, router, usePage } from "@inertiajs/react";
import { routeTenant } from "../../app.jsx";
import DataTable from "@/Components/DataTable";
import { useState, useEffect } from "react";
import CreateBrandPopup from "./CreateBrandPopup.jsx";
import Toast from "@/Components/Toast";
import Switch from "@/Components/Switch";
import BrandFilters from "./BrandFilters";
import axios from "axios";

export default function Index({ brands, auth }) {
    const [perPage, setPerPage] = useState(10);
    const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
    const [selectedBrand, setSelectedBrand] = useState(null);
    const [brandsData, setBrandsData] = useState(brands?.data || []);
    const [toast, setToast] = useState({
        show: false,
        message: '',
        type: 'success'
    });

    const handleStatusFilter = (status) => {
        const params = new URLSearchParams(window.location.search);
        if (status !== '') {
            params.set('status', status);
        } else {
            params.delete('status');
        }
        params.set('page', 1); // Reset to first page when filtering
        router.get(`${window.location.pathname}?${params.toString()}`);
    };

    // Update brandsData when brands prop changes
    useEffect(() => {
        if (brands?.data) {
            setBrandsData(brands.data);
        }
    }, [brands]);

    // Get flash messages from the backend
    const { flash } = usePage().props;

    useEffect(() => {
        // Check for flash messages from the backend

        if (flash && flash.success) {
            setToast({
                show: true,
                message: flash.success,
                type: 'success'
            });
        } else if (flash && flash.error) {
            setToast({
                show: true,
                message: flash.error,
                type: 'error'
            });
        }
    }, [flash]);

    const columns = [
        {
            key: 'id',
            label: 'ID',
            sortable: true
        },
        {
            key: 'name',
            label: 'Name',
            sortable: true
        },
        {
            key: 'image',
            label: 'Image',
            sortable: false,
            render: (image, item) => (
                <div className="flex justify-center items-center">
                    <img
                        src={image || '/default-profile.png'}
                        alt={`${item.name || 'User'} image`}
                        className="object-cover w-10 h-10 rounded-full"
                    />
                </div>
            )
        },
        {
            key: 'is_active',
            label: 'Status',
            sortable: true,
            render: (is_active, brand) => (
                <div className="flex justify-center items-center">
                    <Switch
                        checked={is_active === 1 || is_active === true}
                        onChange={() => handleToggleStatus(brand)}
                        size="md"
                    />
                    <span className="ml-2 text-xs text-gray-500">
                        {is_active ? 'Active' : 'Inactive'}
                    </span>
                </div>
            )
        },
        {
            key: 'created_at',
            label: 'Created At',
            sortable: true,

        },
        {
            key: 'actions',
            label: 'Actions',
            sortable: false,
            render: (_, brand) => (
                <div className="flex justify-center space-x-2">
                    <button
                        onClick={() => handleEdit(brand)}
                        className="px-3 py-1 text-sm text-blue-600 hover:text-blue-800"
                    >
                        Edit 2
                    </button>
                    <button
                        onClick={() => handleDelete(brand)}
                        className="px-3 py-1 text-sm text-red-600 hover:text-red-800"
                    >
                        Delete
                    </button>
                </div>
            )
        }
    ];



    const handlePerPageChange = (value) => {
        setPerPage(value);
        const params = new URLSearchParams(window.location.search);
        params.set('per_page', value);
        router.get(`${window.location.pathname}?${params.toString()}`);
    };

    const handleSort = (column) => {
        const params = new URLSearchParams(window.location.search);
        params.set('order_by', column);
        router.get(`${window.location.pathname}?${params.toString()}`);
    };

    const handleDelete = (brand) => {
        if (confirm('Are you sure you want to delete this brand?')) {
            router.delete(route('tenant.dashboard.brands.destroy', { tenant: route().params.tenant, brand: brand.id }));
        }
    };

    const handleEdit = (brand) => {
        setSelectedBrand(brand);
        setIsCreateModalOpen(true);
    };

    const handleModalClose = () => {
        setIsCreateModalOpen(false);
        setSelectedBrand(null);
    };

    const handleSuccess = (updatedBrand) => {
        // Update the brands data with the new brand
        setBrandsData(prevBrands => {
            // Make sure prevBrands is an array
            const brandsArray = Array.isArray(prevBrands) ? prevBrands : [];
            return brandsArray.map(brand =>
                brand.id === updatedBrand.id ? updatedBrand : brand
            );
        });

        handleModalClose();
        setToast({
            show: true,
            message: 'Brand updated successfully',
            type: 'success'
        });
    };

    const handleToggleStatus = (brand) => {
        // Store the current state for potential revert
        const currentState = Array.isArray(brandsData) ? [...brandsData] : [];

        // Optimistically update the UI first
        const updatedBrands = currentState.map(b =>
            b.id === brand.id ? { ...b, is_active: !b.is_active } : b
        );
        setBrandsData(updatedBrands);

        // Use Inertia's visit method with PATCH
        router.visit(
            routeTenant('tenant.dashboard.brands.toggle-status', {
                tenant: route().params.tenant,
                id: brand.id
            }),
            {
                method: 'patch',
                preserveState: true,
                preserveScroll: true,
                preserveSearch: true,
                onSuccess: (page) => {
                    setToast({
                        show: true,
                        message: page.props.flash.success || 'Status updated successfully',
                        type: 'success'
                    });
                },
                onError: (error) => {
                    // Revert to the previous state
                    setBrandsData(currentState);
                    setToast({
                        show: true,
                        message: error.message || 'Failed to update brand status',
                        type: 'error'
                    });
                }
            }
        );
    };

    return (
        <>
            <Head title="Brands" />

            <Toast
                show={toast.show}
                message={toast.message}
                type={toast.type}
                onClose={() => setToast({...toast, show: false})}
            />

            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-2xl font-semibold text-gray-900">Brands</h2>
                                <button
                                    onClick={() => setIsCreateModalOpen(true)}
                                    className="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md border border-transparent shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Add New Brand
                                </button>
                            </div>

                            <BrandFilters
                                onReset={() => {
                                    const urlParams = new URLSearchParams(window.location.search);
                                    if (urlParams.get('per_page')) {
                                        router.get(`${window.location.pathname}?per_page=${urlParams.get('per_page')}`);
                                    } else {
                                        router.get(window.location.pathname);
                                    }
                                }}
                            />

                            <DataTable
                                data={brandsData}
                                columns={columns}
                                meta={brands.meta}
                                links={brands.links}
                                onSort={handleSort}
                                onSearch={(value) => {
                                    const params = new URLSearchParams(window.location.search);
                                    if (value) {
                                        params.set('search', value);
                                    } else {
                                        params.delete('search');
                                    }
                                    params.set('page', 1);
                                    router.get(`${window.location.pathname}?${params.toString()}`);
                                }}
                                onPerPageChange={handlePerPageChange}
                                perPage={perPage}
                                searchPlaceholder="Search brands..."
                            />

                            {isCreateModalOpen && (
                                <CreateBrandPopup
                                    onClose={handleModalClose}
                                    successCallback={handleSuccess}
                                    brand={selectedBrand}
                                />
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
