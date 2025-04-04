import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/react/20/solid';

export default function DataTable({
    data,
    columns,
    meta,
    links,
    onSort,
    onSearch,
    onDelete,
    onEdit,
    onPerPageChange,
    perPage = 10,
    searchPlaceholder = "Search...",
}) {
    const [searchTerm, setSearchTerm] = useState('');

    // Ensure data is an array
    const tableData = Array.isArray(data.data) ? data.data : [];

    const handleSearch = (e) => {
        const value = e.target.value;
        setSearchTerm(value);
        onSearch && onSearch(value);
    };

    const handleSort = (column) => {
        if (column.sortable && onSort) {
            onSort(column.key);
        }
    };

    return (
        <div className="flex flex-col">
            {/* Search and other controls */}
            <div className="flex justify-between items-center mb-4">
                <div className="flex gap-4 items-center">
                    <div className="flex-1 max-w-sm">
                        <input
                            type="text"
                            placeholder={searchPlaceholder}
                            className="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            value={searchTerm}
                            onChange={handleSearch}
                        />
                    </div>
                    <div className="flex gap-2 items-center">
                        <label htmlFor="perPage" className="text-sm text-gray-600">Per page:</label>
                        <select
                            id="perPage"
                            className="rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            value={perPage}
                            onChange={(e) => onPerPageChange && onPerPageChange(Number(e.target.value))}
                        >
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>

            {/* Table */}
            <div className="overflow-x-auto rounded-lg border border-gray-200">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            {columns.map((column, index) => (
                                <th
                                    key={index}
                                    scope="col"
                                    className={`px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ${
                                        column.sortable ? 'cursor-pointer hover:text-gray-700' : ''
                                    }`}
                                    onClick={() => handleSort(column)}
                                >
                                    {column.label}
                                    {column.sortable && (
                                        <span className="ml-2">⇅</span>
                                    )}
                                </th>
                            ))}
                            {(onEdit || onDelete) && (
                                <th scope="col" className="relative px-6 py-3">
                                    <span className="sr-only">Actions</span>
                                </th>
                            )}
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {tableData.length > 0 ? (
                            tableData.map((item, rowIndex) => (
                                <tr key={rowIndex} className="hover:bg-gray-50">
                                    {columns.map((column, colIndex) => (
                                        <td
                                            key={colIndex}
                                            className="px-6 py-4 text-sm text-gray-900 whitespace-nowrap"
                                        >
                                            {column.render
                                                ? column.render(item[column.key], item)
                                                : item[column.key]}
                                        </td>
                                    ))}
                                    {(onEdit || onDelete) && (
                                        <td className="px-6 py-4 space-x-2 text-sm font-medium text-right whitespace-nowrap">
                                            {onEdit && (
                                                <button
                                                    onClick={() => onEdit(item)}
                                                    className="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Edit
                                                </button>
                                            )}
                                            {onDelete && (
                                                <button
                                                    onClick={() => onDelete(item)}
                                                    className="text-red-600 hover:text-red-900"
                                                >
                                                    Delete
                                                </button>
                                            )}
                                        </td>
                                    )}
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td
                                    colSpan={columns.length + (onEdit || onDelete ? 1 : 0)}
                                    className="px-6 py-12 text-center"
                                >
                                    <div className="flex flex-col items-center">
                                        <svg
                                            className="w-12 h-12 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                            />
                                        </svg>
                                        <h3 className="mt-2 text-sm font-medium text-gray-900">No data available</h3>
                                        <p className="mt-1 text-sm text-gray-500">Try adjusting your search or filters.</p>
                                    </div>
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Pagination */}
            {meta?.links && links && tableData.length > 0 && (
                <div className="flex justify-between items-center px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                    <div className="flex flex-1 justify-between sm:hidden">
                        <Link
                            preserveScroll
                            preserveState
                            href={links.prev_page_url || '#'}
                            className={`relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md ${
                                !links.prev_page_url
                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    : 'bg-white text-gray-700 hover:bg-gray-50'
                            }`}
                        >
                            Previous
                        </Link>
                        <Link
                            preserveScroll
                            preserveState
                            href={links.next_page_url || '#'}
                            className={`relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md ${
                                !links.next_page_url
                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                    : 'bg-white text-gray-700 hover:bg-gray-50'
                            }`}
                        >
                            Next
                        </Link>
                    </div>
                    <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p className="text-sm text-gray-700">
                                Showing{' '}
                                <span className="font-medium">{meta?.from || 0}</span>{' '}
                                to{' '}
                                <span className="font-medium">{meta?.to || 0}</span>{' '}
                                of{' '}
                                <span className="font-medium">{meta?.total || 0}</span>{' '}
                                results
                            </p>
                        </div>
                        <div>
                            <nav className="inline-flex relative z-0 -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <Link
                                    preserveScroll
                                    preserveState
                                    href={meta?.links?.[0]?.url || '#'}
                                    className={`relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium ${
                                        !links.prev_page_url
                                            ? 'text-gray-300 cursor-not-allowed'
                                            : 'text-gray-500 hover:bg-gray-50'
                                    }`}
                                >
                                    <span className="sr-only">Previous</span>
                                    <ChevronLeftIcon className="w-5 h-5" aria-hidden="true" />
                                </Link>

                                {meta?.links?.slice(1, -1).map((link, index) => (
                                    <Link
                                        key={index}
                                        preserveScroll
                                        preserveState
                                        href={link.url || '#'}
                                        className={`relative inline-flex items-center px-3 py-2 border text-sm font-medium ${
                                            !link.url
                                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                : link.active
                                                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                        }`}
                                    >
                                        <span dangerouslySetInnerHTML={{ __html: link.label }} />
                                    </Link>
                                ))}
                                <Link
                                    preserveScroll
                                    preserveState
                                    href={meta?.links?.[meta.links.length - 1]?.url || '#'}
                                    className={`relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium ${
                                        !links.next_page_url
                                            ? 'text-gray-300 cursor-not-allowed'
                                            : 'text-gray-500 hover:bg-gray-50'
                                    }`}
                                >
                                    <span className="sr-only">Next</span>
                                    <ChevronRightIcon className="w-5 h-5" aria-hidden="true" />
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
