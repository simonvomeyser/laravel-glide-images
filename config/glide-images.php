<?php

return [

    /**
     * The endpoint to access the transformed images, e.g. /glide/{path}
     * Only change this if you know need the "glide" default endpoint.
     */
    'endpoint' => 'glide',

    /**
     * The path to the folder where the images are stored once transformed.
     */
    'cache' => '.glide-cache',

    /**
     * It is highly recommended that you use signed URLs in production environments
     * Otherwise your application will be open to mass image-resize attacks.
     */
    'secure' => true,

    /**
     * This is a security measure to prevent users from resizing huge images
     */
    'max_image_size' => 2000 * 2000,

    /**
     * The default quality of the images
     */
    'quality' => 80,

    /**
     * The default fit, e.g. max, fill, crop, etc.
     * "max" will resize the image to fit within the given
     * dimensions while maintaining the original aspect ratio
     *
     * https://glide.thephpleague.com/1.0/api/size/
     */
    'fit' => 'max',

];
