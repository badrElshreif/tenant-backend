import React from 'react';
import Modal from "../../Components/Modal.jsx";
import CreateBrandForm from './CreateBrandForm';

export default function CreateBrandPopup({ isOpen, onClose, successCallback }) {
    return (
        <Modal
            isOpen={isOpen}
            onClose={onClose}
            title="Create New Brand"
            maxWidth="full"
            // fullWidth={true}
            fullHeight={true}
        >
            <CreateBrandForm
                onClose={onClose}
                successCallback={successCallback}
            />
        </Modal>
    );
}
