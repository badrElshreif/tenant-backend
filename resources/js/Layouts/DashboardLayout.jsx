import React, { useState } from 'react';
import { usePage } from '@inertiajs/react';
import Sidebar from './Sidebar';

export default function DashboardLayout({ children }) {
    const [sidebarOpen, setSidebarOpen] = useState(true);
    const { url } = usePage();

    const menuItems = [
        {
            name: 'Dashboard',
            icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            href: '/dashboard',
        },
        {
            name: 'Catalog',
            icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
            href: '/dashboard/catalog',
            submenu: [
                { name: 'Categories', href: '/dashboard/categories' },
                { name: 'Brands', href: '/dashboard/brands' }
            ]
        },
        {
            name: 'Products',
            icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            href: '/dashboard/products',
            submenu: [
                { name: 'All Products', href: '/dashboard/products' },
                { name: 'Add Product', href: '/dashboard/products/create' }
            ]
        },
        {
            name: 'Orders',
            icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            href: '/dashboard/orders',
            submenu: [
                { name: 'All Orders', href: '/dashboard/orders' },
                { name: 'Pending', href: '/dashboard/orders/pending' },
                { name: 'Completed', href: '/dashboard/orders/completed' }
            ]
        }
    ];

    return (
        <div className="min-h-screen bg-gray-100">
            <Sidebar
                isOpen={sidebarOpen}
                setIsOpen={setSidebarOpen}
                menuItems={menuItems}
                currentPath={url}
            />

            <div className={`transition-all duration-300 ${sidebarOpen ? 'ml-64' : 'ml-20'}`}>
                <header className="bg-white shadow">
                    <div className="flex items-center justify-between px-4 py-6">
                        <button
                            onClick={() => setSidebarOpen(!sidebarOpen)}
                            className="text-gray-500 hover:text-gray-600 focus:outline-none"
                        >
                            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {sidebarOpen ? (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                                ) : (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                                )}
                            </svg>
                        </button>
                    </div>
                </header>

                <main className="p-6">
                    {children}
                </main>
            </div>
        </div>
    );
}
