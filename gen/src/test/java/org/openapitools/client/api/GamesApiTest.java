/*
 * RaterApi
 * No description provided (generated by Openapi Generator https://github.com/openapitools/openapi-generator)
 *
 * The version of the OpenAPI document: 1.0.0
 * 
 *
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */


package org.openapitools.client.api;

import org.openapitools.client.ApiException;
import org.openapitools.client.model.InlineObject;
import org.openapitools.client.model.InlineResponse201;
import org.openapitools.client.model.InlineResponse400;
import org.openapitools.client.model.InlineResponse401;
import org.junit.Test;
import org.junit.Ignore;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * API tests for GamesApi
 */
@Ignore
public class GamesApiTest {

    private final GamesApi api = new GamesApi();

    
    /**
     * Create a game
     *
     * Create a game by name
     *
     * @throws ApiException
     *          if the Api call fails
     */
    @Test
    public void authLoginTest() throws ApiException {
        InlineObject inlineObject = null;
        InlineResponse201 response = api.authLogin(inlineObject);

        // TODO: test validations
    }
    
}