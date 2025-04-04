import React, { useState, useCallback } from 'react';
import { router } from '@inertiajs/react';
import { debounce } from 'lodash';

export default function BrandFilters({ onReset }) {
    const urlParams = new URLSearchParams(window.location.search);
    const [searchTerm, setSearchTerm] = useState(urlParams.get('search') || '');
    
    const handleStatusFilter = (status) => {
        const params = new URLSearchParams(window.location.search);
        if (status !== '') {
            params.set('status', status);
        } else {
            params.delete('status');
        }
        params.set('page', 1);
        router.get(`${window.location.pathname}?${params.toString()}`);
    };

    // Debounced search function
    const debouncedSearch = useCallback(
        debounce((value) => {
            const params = new URLSearchParams(window.location.search);
            if (value) {
                params.set('search', value);
            } else {
                params.delete('search');
            }
            params.set('page', 1);
            router.get(`${window.location.pathname}?${params.toString()}`);
        }, 300),
        []
    );

    const handleSearch = (value) => {
        setSearchTerm(value);
        debouncedSearch(value);
    };

    const handleReset = () => {
        setSearchTerm('');
        router.get(window.location.pathname);
        onReset?.();
    };

    const statusOptions = [
        { value: '1', label: 'Active' },
        { value: '0', label: 'Inactive' }
    ];

    return (
        <div className="bg-white p-4 rounded-lg shadow mb-4">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label htmlFor="search" className="block text-sm font-medium text-gray-700 mb-1">
                        Search
                    </label>
                    <input
                        type="text"
                        id="search"
                        placeholder="Search brands..."
                        className="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        value={searchTerm}
                        onChange={(e) => handleSearch(e.target.value)}
                    />
                </div>
                
                <div>
                    <label htmlFor="status" className="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>
                    <select
                        id="status"
                        className="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        onChange={(e) => handleStatusFilter(e.target.value)}
                        value={urlParams.get('status') || ''}
                    >
                        <option value="">All Status</option>
                        {statusOptions.map((option) => (
                            <option key={option.value} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </select>
                </div>

                <div className="flex items-end">
                    <button
                        onClick={handleReset}
                        type="button"
                        className="w-full rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                    >
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>
    );
}
