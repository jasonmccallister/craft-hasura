# Hasura

Use your Craft CMS credentials to authenticate with a GraphQL API powered by Hasura.io

![Hasura Logo](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1.  Open your terminal and go to your Craft project:

        cd /path/to/project

2.  Then tell Composer to load the plugin:

        composer require jasonmccallister/hasura

3.  In the Control Panel, go to Settings → Plugins and click the “Install” button for Hasura.

## Hasura Overview

Hasura.io is an open-source tool that makes building APIs with real-time GraphQL APIs without writing _any code_. Hasura lets you connect to a new, or existing, PostgreSQL database and automatically build a GraphQL schema with real-time subscriptions; all with out writing any code!

This allows you to build GraphQL APIs at scale, with no code, using only a database and Docker image!

_Watch this video as the Hasura team takes a complex applications database (in the example it uses the Gitlab database) and drops it into GraphQL in under 4 minutes_

[![Instant GraphQL on GitLab](https://i.ytimg.com/vi/a2AhxKqd82Q/maxresdefault.jpg)](https://www.youtube.com/watch?v=a2AhxKqd82Q)

Out of the box, Hasura comes with:

1. Automatic Schema generation including nested relationships
2. GraphQL queries & mutations based on your database
3. Subscription support allowing realtime UI updates
4. Remote schemas allowing you to combine multiple GraphQL APIs
5. Migrations for new projects
6. Event trigger based on database actions (insert, updates, and deletes)
7. Dynamic access roles and authentication

However, Hasura can be configured to accept JWTs that are signed in a specific format. This is where the Hasura plugin helps. This plugin allows you to use your Craft CMS users and groups to generate the JWT to send to your Hasura API.

## Configuring Hasura

After installation, you need to set a few items in the plugins settings:

1. Enable or disable CSRF (if external applications will use this endpoint for JWTs you need to disable CSRF)
2. Set the signing method on the JWT that the Hasura API expects (RS256 or etc)
3. Enter the key (string or private token)

## Using Craft CMS as Authentication for Hasura

Users will authenticate with their username (or email if Craft is configured for email) and password to

`https://yourdomain.com/hasura/auth`

### Example Response

```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwNGZjNDM5Mi0wMmNlLTQ3MTgtYmQ5My03ODhjMWI1ZTU1ZjQiLCJhZG1pbiI6dHJ1ZSwiaWF0IjoxNTUzMDc5MjY5LCJleHAiOjE1NTMwODI4NjksImh0dHBzOlwvXC9oYXN1cmEuaW9cL2p3dFwvY2xhaW1zIjp7IngtaGFzdXJhLWFsbG93ZWQtcm9sZXMiOlsidXNlciIsImFkbWluIl0sIngtaGFzdXJhLWRlZmF1bHQtcm9sZSI6ImFkbWluIiwieC1oYXN1cmEtdXNlci1pZCI6IjA0ZmM0MzkyLTAyY2UtNDcxOC1iZDkzLTc4OGMxYjVlNTVmNCJ9fQ.WEAFZYon5arnCTN9ecAEiG4dKl-jkyk3em8EpJ9N0Vs"
}
```

### Example JWT

```json
{
  "sub": "04fc4392-02ce-4718-bd93-788c1b5e55f4",
  "admin": true,
  "iat": 1553079269,
  "exp": 1553082869,
  "https://hasura.io/jwt/claims": {
    "x-hasura-allowed-roles": ["user", "admin"],
    "x-hasura-default-role": "admin",
    "x-hasura-user-id": "04fc4392-02ce-4718-bd93-788c1b5e55f4"
  }
}
```

## Configuring Craft CMS to Handle Event Trigger Webhooks from Hasura

Hasura allows you to tie events to table actions like insert, update, and delete. These are known as [Event Triggers](https://hasura.io/event-triggers). This plugin allows you to receive those event triggers by configuring Hasura to send to your Craft CMS.

`https://yourdomain.com/hasura/webhook`

You can configure the header that Hasura will send in the plugin settings as well as a key. When the event trigger payload is received, the plugin will fire the `hasuraEventTrigger` event and allow you to listen for the event and take action.

The event contains the following:

- table: The name of the table the event came from (e.g. `todos`)
- trigger: The name of the trigger (e.g. `send_updated_todo_to_craft`)
- payload: The payload of the event, which is [contains the new and old data](https://docs.hasura.io/1.0/graphql/manual/event-triggers/payload.html#json-payload) (based on the trigger type)

Brought to you by [Jason McCallister](https://mccallister.io)

## Add Custom Claims via Twig field in Plugin Settings

You can add custom claims (for example the users name or a custom field) to the JWT token via the new Custom Claims field in the Plugin settings.
You can add any additional information as well as user specific details as he field accepts the `user` variable. In Hasura you are then able to write rules based on the `x-hasura-custom-claim` object.

### Example twig query

```twig
{% set customCategory = user.customCategory.one() %}
{% if customCategory %}
  {% set jsonObject = { "uid": customCategory, "title": customCategory, "slug": customCategory } %}
  {{ jsonObject |json_encode() }}
{% endif %}
```

### Example JWT

```json
{
  "sub": "04fc4392-02ce-4718-bd93-788c1b5e55f4",
  "admin": true,
  "iat": 1553079269,
  "exp": 1553082869,
  "https://hasura.io/jwt/claims": {
    "x-hasura-allowed-roles": ["user", "admin"],
    "x-hasura-default-role": "admin",
    "x-hasura-user-id": "04fc4392-02ce-4718-bd93-788c1b5e55f4",
    "x-hasura-custom-claim": {
      "uid": "071cd618-e675-4bcc-b362-0311b43333c9",
      "title": "Category Name",
      "slug": "category-name"
    }
  }
}
```
