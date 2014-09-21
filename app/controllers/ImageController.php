<?php

use Zidisha\Upload\UploadQuery;

class ImageController extends BaseController
{
    public function getImage($uploadId, $format, $isProfileImage)
    {
        $upload = UploadQuery::create()
            ->filterById($uploadId)
            ->findOne();

        if (!$upload || !$upload->isImage()) {
            App::abort(404, 'Bad Request');
        }

        $file = $upload->resize($format, $isProfileImage);

        return Response::make(file_get_contents($file->getPathname()), 200, ['content-type' => $file->getMimeType()]);
    }
}
