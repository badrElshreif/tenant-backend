import React, { useState } from 'react';
import Modal from "../../Components/Modal.jsx";
import CreateBrandForm from './CreateBrandForm';
import Toast from "@/Components/Toast";

export default function CreateBrandPopup({ onClose, successCallback, brand = null }) {
    const [toast, setToast] = useState({
        show: false,
        message: '',
        type: 'success'
    });

    const handleSuccess = (message) => {
        // Show success toast
        setToast({
            show: true,
            message: message,
            type: 'success'
        });

        // Call the parent success callback if provided
        if (successCallback) {
            successCallback(message);
        }
    };

    const isEditMode = !!brand;

    return (
        <>
            <Toast
                show={toast.show}
                message={toast.message}
                type={toast.type}
                onClose={() => setToast({...toast, show: false})}
            />

            <Modal
                isOpen={true}
                onClose={onClose}
                title={isEditMode ? "Edit Brand" : "Create New Brand"}
                maxWidth="full"
                fullHeight={true}
            >
                <CreateBrandForm
                    onClose={onClose}
                    successCallback={handleSuccess}
                    brand={brand}
                />
            </Modal>
        </>
    );
}
