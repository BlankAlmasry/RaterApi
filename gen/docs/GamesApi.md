# GamesApi

All URIs are relative to *http://localhost*

Method | HTTP request | Description
------------- | ------------- | -------------
[**authLogin**](GamesApi.md#authLogin) | **POST** /api/games | Create a game


<a name="authLogin"></a>
# **authLogin**
> InlineResponse201 authLogin(inlineObject)

Create a game

Create a game by name

### Example
```java
// Import classes:
import org.openapitools.client.ApiClient;
import org.openapitools.client.ApiException;
import org.openapitools.client.Configuration;
import org.openapitools.client.models.*;
import org.openapitools.client.api.GamesApi;

public class Example {
  public static void main(String[] args) {
    ApiClient defaultClient = Configuration.getDefaultApiClient();
    defaultClient.setBasePath("http://localhost");

    GamesApi apiInstance = new GamesApi(defaultClient);
    InlineObject inlineObject = new InlineObject(); // InlineObject | 
    try {
      InlineResponse201 result = apiInstance.authLogin(inlineObject);
      System.out.println(result);
    } catch (ApiException e) {
      System.err.println("Exception when calling GamesApi#authLogin");
      System.err.println("Status code: " + e.getCode());
      System.err.println("Reason: " + e.getResponseBody());
      System.err.println("Response headers: " + e.getResponseHeaders());
      e.printStackTrace();
    }
  }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **inlineObject** | [**InlineObject**](InlineObject.md)|  |

### Return type

[**InlineResponse201**](InlineResponse201.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
**201** | Wrong credentials response |  -  |
**400** | Wrong credentials response |  -  |
**401** | Unathourized request, you need api token |  -  |

