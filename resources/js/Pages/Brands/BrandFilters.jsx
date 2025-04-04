import React from 'react';
import { router } from '@inertiajs/react';

export default function BrandFilters({ onReset }) {
    const urlParams = new URLSearchParams(window.location.search);

    const handleStatusFilter = (status) => {
        const params = new URLSearchParams(window.location.search);
        if (status !== '') {
            params.set('status', status);
        } else {
            params.delete('status');
        }
        params.set('page', 1);
        router.get(`${window.location.pathname}?${params.toString()}`);
        onStatusFilter?.(status);
    };

    const handleReset = () => {
        router.get(window.location.pathname);
        onReset?.();
    };

    const statusOptions = [
        { value: '1', label: 'Active', color: 'bg-green-100 text-green-800' },
        { value: '0', label: 'Inactive', color: 'bg-gray-100 text-gray-800' }
    ];

    const currentStatus = urlParams.get('status') || '';

    return (
        <div className="overflow-hidden bg-white rounded-lg shadow">
            <div className="p-4 border-b border-gray-200">
                <h3 className="text-lg font-medium text-gray-900">Filters</h3>
            </div>
            <div className="p-4 space-y-4">
                {/* Status Filter Section */}
                <div>
                    <label className="flex justify-between items-center text-sm font-medium text-gray-700">
                        <span>Status</span>
                        {currentStatus && (
                            <button
                                onClick={handleReset}
                                className="flex items-center text-xs text-gray-500 hover:text-gray-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="mr-1 w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
                                </svg>
                                Clear
                            </button>
                        )}
                    </label>
                    <div className="flex flex-wrap gap-2 mt-2">
                        <button
                            onClick={() => handleStatusFilter('')}
                            className={`px-3 py-1 rounded-full text-sm font-medium transition-colors duration-150 ${!currentStatus ? 'text-indigo-800 bg-indigo-100 ring-2 ring-indigo-600' : 'text-gray-800 bg-gray-100 hover:bg-gray-200'}`}
                        >
                            All
                        </button>
                        {statusOptions.map((option) => (
                            <button
                                key={option.value}
                                onClick={() => handleStatusFilter(option.value)}
                                className={`px-3 py-1 rounded-full text-sm font-medium transition-colors duration-150 ${currentStatus === option.value ? 'ring-2 ring-indigo-600 ' + option.color : option.color + ' hover:bg-opacity-80'}`}
                            >
                                {option.label}
                            </button>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
