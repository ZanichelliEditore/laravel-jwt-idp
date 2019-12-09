<?php

    //Generic API description for documentation purposes. Each call's documentation is contained in the relevant controller.
    /**
     * 
     * @OA\Info(
     *     version="1.0.0",
     *     title="Zanichelli API IdentityProvider",
     *     description="REST APIs to use SSO (single-sign-on) for applications ",
     *     @OA\Contact(
     *         name="Zanichelli DEV team", 
     *         email="developers@zanichelli.it"
     *     ),
     * )
     * 
     */
    /**
     * @OA\Tag(
     *     name="JWT Auth",
     *     description="Handle login operations",
     * )
     *
    */

    /**
     * @OA\SecurityScheme(
     *     type="oauth2",
     *     name="passport",
     *     securityScheme="passport",
     *     in="header",
     *     scheme={"http","https"},
     *     @OA\Flow(
     *         flow="clientCredentials",
     *         tokenUrl=L5_SWAGGER_CONST_TOKEN_URL,
     *         scopes={}
     *     )
     *  )
     */
