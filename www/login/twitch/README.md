# Twitch Authentication PHP Sample
Here you will find a simple PHP application illustrating how to authenticate Twitch API calls using the OAuth authorization code flow.  This sample uses [The PHP League's OAuth 2 Client](https://github.com/thephpleague/oauth2-client).

## Installation
After you have cloned this repository, use [Composer](https://getcomposer.org/) to install the OAuth library.

```sh
$ composer require league/oauth2-client
```

## Structure
This sample contains two files:

1. twitch.php - This is the actual Twitch OAuth2 provider class, using the abstract provider class as a base.
2. index.php - This file uses twitch.php to actually authenticate the user.

## Usage
Before running this sample, you will need to set four configuration fields at the top of index.php:

1. clientId - This is the Client ID of your registered application.  You can register an application at [https://www.twitch.tv/settings/connections]
2. clientSecret - This is the secret generated for you when you register your application, do not share this. In a production environment, it is STRONGLY recommended that you do not store application secrets on your file system or in your source code.
4. redirectUri - This is the callback URL you supply when you register your application.

Optionally, you may set the scopes requested in the scopes field.

After setting these fields, you may run the sample in any local or hosted PHP environment you prefer.

## Next Steps
From here you can add as many pages as you want and create a real web app for Twitch users.

## License

Copyright 2017 Amazon.com, Inc. or its affiliates. All Rights Reserved.

Licensed under the Apache License, Version 2.0 (the "License"). You may not use this file except in compliance with the License. A copy of the License is located at

    http://aws.amazon.com/apache2.0/

or in the "license" file accompanying this file. This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License. 
