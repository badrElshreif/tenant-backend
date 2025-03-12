import { Head, Link, router } from "@inertiajs/react";
import { routeTenant } from "../../app.jsx";
import DataTable from "@/Components/DataTable";
import { useState } from "react";
import CreateBrandPopup from "./CreateBrandPopup.jsx";

export default function Index({ brands, auth }) {
    const [search, setSearch] = useState('');
    const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);

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
                <div className="flex items-center justify-center">
                    <img
                        src={image || '/default-profile.png'}
                        alt={`${item.name || 'User'} image`}
                        className="h-10 w-10 rounded-full object-cover"
                    />
                </div>
            )
        },
        {
            key: 'status',
            label: 'Status',
            sortable: true,
            render: (status) => (
                <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                    status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                }`}>
                    {status ? 'Active' : 'Inactive'}
                </span>
            )
        },
        {
            key: 'created_at',
            label: 'Created At',
            sortable: true,

        }
    ];

    const handleSearch = (value) => {
        setSearch(value);
        router.get(
            route('tenant.dashboard.brands.index', route().params.tenant),
            { search: value },
            { preserveState: true }
        );
    };

    const handleSort = (column) => {
        router.get(
            route('tenant.dashboard.brands.index', route().params.tenant),
            { order_by: column },
            { preserveState: true }
        );
    };

    const handleDelete = (brand) => {
        if (confirm('Are you sure you want to delete this brand?')) {
            router.delete(route('tenant.dashboard.brands.destroy', { tenant: route().params.tenant, brand: brand.id }));
        }
    };

    const handleEdit = (brand) => {
        router.get(route('tenant.dashboard.brands.edit', { tenant: route().params.tenant, brand: brand.id }));
    };

    const refreshData = () => {
        // Logic to refresh your data/table after successful creation
        // For example:
        // fetchBrands().then(data => setBrands(data));
    };

    return (
        <>
            <Head title="Brands" />
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-2xl font-semibold text-gray-900">Brands</h2>
                                <button
                                    onClick={() => setIsCreateModalOpen(true)}
                                    className="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Add New Brand
                                </button>
                            </div>

                            <DataTable
                                data={brands.data}
                                columns={columns}
                                meta={brands.meta}
                                links={brands.links}
                                onSort={handleSort}
                                onSearch={handleSearch}
                                onDelete={handleDelete}
                                onEdit={handleEdit}
                                searchPlaceholder="Search brands..."
                            />

                            <CreateBrandPopup
                                isOpen={isCreateModalOpen}
                                onClose={() => setIsCreateModalOpen(false)}
                                successCallback={refreshData}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

