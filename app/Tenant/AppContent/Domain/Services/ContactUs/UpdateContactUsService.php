<?php

namespace App\Tenant\AppContent\Domain\Services\ContactUs;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\ContactUs;
use App\Tenant\AppContent\Domain\Resources\ContactUsResource;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;

class UpdateContactUsService extends Service
{
    public function handle($data = [])
    {
        try {
            $contact_us = ContactUs::findOrFail($data['contact_id']);
            if($contact_us->parent_id !== null)
                return new GenericPayload(
                    __('error.invalidMsg'), 422
                );
            $contact_us->update([
                'read_at' => now(),
                'read_by' => auth('admin')->id()
            ]);
            $data['parent_id'] = $contact_us->id;
            ContactUs::create($data);
            Mail::to($contact_us->email)->send(new \App\Tenant\AppContent\Domain\Mail\ReplyToContactUs( $data['body'] , $contact_us->title ));
            return new GenericPayload($contact_us , Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }

    }
}
