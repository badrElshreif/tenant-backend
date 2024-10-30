<?php

namespace App\Uploader\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Uploader\Domain\Models\Attachment;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class DeleteAttachmentService extends Service
{
    public function handle($data = []) 
    {
        try {

            $attachment = Attachment::findOrFail($data['attachment_id']);
            if($attachment->creatable == auth()->user()){
            	$attachment->delete();
            	return new GenericPayload(['message' => __('success.deletedSuccessfuly')]);
            } else{
            	return new GenericPayload( __('error.cannotDeleteFile'), 422);
            }

            
            //return new GenericPayload(['message' => __('success.deletedSuccessfuly')], Response::HTTP_NO_CONTENT);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (\Exception $ex) {
            return new GenericPayload(
                $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}