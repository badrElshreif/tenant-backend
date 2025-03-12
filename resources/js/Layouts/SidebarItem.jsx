import React, { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';

export default function SidebarItem({ item, isOpen, currentPath }) {
    const [isSubmenuOpen, setIsSubmenuOpen] = useState(false);

    // Check if current path matches item or any of its submenu items
    useEffect(() => {
        if (item.submenu) {
            const isActive = item.submenu.some(subItem => currentPath.startsWith(subItem.href));
            setIsSubmenuOpen(isActive);
        }
    }, [currentPath]);

    const isActive = (href) => currentPath.startsWith(href);

    return (
        <div>
            {item.submenu ? (
                // Menu item with submenu
                <div>
                    <button
                        onClick={() => setIsSubmenuOpen(!isSubmenuOpen)}
                        className={`w-full flex items-center px-4 py-3 transition-colors duration-200
                            ${isActive(item.href) ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'}`}
                    >
                        <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={item.icon} />
                        </svg>

                        {isOpen && (
                            <>
                                <span className="ml-3">{item.name}</span>
                                <svg
                                    className={`ml-auto h-4 w-4 transform transition-transform duration-200 ${isSubmenuOpen ? 'rotate-180' : ''}`}
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                                </svg>
                            </>
                        )}
                    </button>

                    {/* Submenu */}
                    <div className={`transition-all duration-300 overflow-hidden ${isSubmenuOpen ? 'max-h-screen' : 'max-h-0'}`}>
                        {item.submenu.map((subItem, index) => (
                            <Link
                                key={index}
                                href={subItem.href}
                                className={`block pl-12 pr-4 py-2 transition-colors duration-200
                                    ${isActive(subItem.href)
                                        ? 'bg-gray-700 text-white'
                                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'}`}
                            >
                                {isOpen && subItem.name}
                            </Link>
                        ))}
                    </div>
                </div>
            ) : (
                // Regular menu item
                <Link
                    href={item.href}
                    className={`flex items-center px-4 py-3 transition-colors duration-200
                        ${isActive(item.href) ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'}`}
                >
                    <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={item.icon} />
                    </svg>
                    {isOpen && <span className="ml-3">{item.name}</span>}
                </Link>
            )}
        </div>
    );
}
