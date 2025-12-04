<?php

namespace App\Services;

use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Log;

class AwsRekognitionService
{
    protected $client;

    public function __construct()
    {
        $this->client = new RekognitionClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }


    /**
     * INDEX FACE (For student registration)
     */
    public function indexFace($imageBytes, $studentId)
    {
        try {

            $res = $this->client->indexFaces([
                'CollectionId'      => env('AWS_REKOGNITION_COLLECTION'),
                'ExternalImageId'   => (string) $studentId,
                'Image'             => ['Bytes' => $imageBytes],
                'DetectionAttributes' => ['DEFAULT']
            ]);

            // Debug log
            Log::info("IndexFace Response", $res->toArray());

            return $res['FaceRecords'][0]['Face']['FaceId'] ?? null;

        } catch (\Exception $e) {
            Log::error("Rekognition Index Error: " . $e->getMessage());
            return null;
        }
    }


    /**
     * SEARCH FACE (on cropped face image)
     */
    public function searchFaces($imageBytes)
    {
        try {
            $result = $this->client->searchFacesByImage([
                'CollectionId'       => env('AWS_REKOGNITION_COLLECTION'),
                'Image'              => ['Bytes' => $imageBytes],
                'FaceMatchThreshold' => 80,
                'MaxFaces'           => 15
            ]);

            // Debug log
            Log::info("SearchFaces Response", $result->toArray());

            return $result['FaceMatches'] ?? [];

        } catch (\Exception $e) {
            Log::error("Rekognition Search Error: " . $e->getMessage());
            return [];
        }
    }


    /**
     * DETECT FACES in group image (before cropping)
     */
    public function detectFaces($imageBytes)
    {
        try {

            $response = $this->client->detectFaces([
                'Image' => ['Bytes' => $imageBytes],
                'Attributes' => ['DEFAULT']
            ]);

            // Debug log
            Log::info("DetectFaces Bounding Boxes", $response->toArray());

            return $response;

        } catch (\Exception $e) {
            Log::error("Rekognition detectFaces Error: " . $e->getMessage());
            return [];
        }
    }


    /**
     * DELETE FACE from AWS collection
     */
    public function deleteFace($faceId)
    {
        try {
            $result = $this->client->deleteFaces([
                'CollectionId' => env('AWS_REKOGNITION_COLLECTION'),
                'FaceIds'      => [$faceId]
            ]);

            Log::info("DeleteFace Response", $result->toArray());

            return $result;

        } catch (\Exception $e) {
            Log::error("Rekognition Delete Error: " . $e->getMessage());
            return null;
        }
    }
}
