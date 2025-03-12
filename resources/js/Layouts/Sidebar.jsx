import React, { useRef, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import SidebarItem from './SidebarItem';

export default function Sidebar({ isOpen, menuItems, currentPath }) {
    const sidebarRef = useRef(null);

    useEffect(() => {
        const sidebar = sidebarRef.current;
        if (!sidebar) return;

        // Add custom scrollbar styles
        sidebar.style.scrollbarWidth = 'thin';
        sidebar.style.scrollbarColor = '#CBD5E0 #EDF2F7';
    }, []);

    return (
        <div
            ref={sidebarRef}
            className={`fixed left-0 top-0 h-full bg-gray-800 text-white transition-all duration-300 overflow-y-auto
                ${isOpen ? 'w-64' : 'w-20'}`}
        >
            <div className="p-4">
                <Link href="/dashboard" className="flex items-center space-x-2">
                    <img src="/logo.svg" alt="Logo" className="h-8 w-8" />
                    {isOpen && <span className="text-xl font-bold">Dashboard</span>}
                </Link>
            </div>

            <nav className="mt-8">
                {menuItems.map((item, index) => (
                    <SidebarItem
                        key={index}
                        item={item}
                        isOpen={isOpen}
                        currentPath={currentPath}
                    />
                ))}
            </nav>
        </div>
    );
}
