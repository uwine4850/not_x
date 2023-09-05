<?php
namespace config;
const PATH_TO_MEDIA_USERS = '/var/www/html/media/users/';
const MAX_IMAGES = 2;
const SEARCH_PROFILE_COUNT = 10;
const LOAD_POST_COUNT = 2;
const LOAD_MSG_COUNT = 20;
const CSRF_TOKEN_LIFETIME = 3600;


enum WS_ACTIONS_CHAT: string{
    case JOIN_CHAT_ROOM = 'JOIN_CHAT_ROOM';
    case GENERATE_CHAT_ID = 'GENERATE_CHAT_ID';
    case REGENERATE_CHAT_ID = 'REGENERATE_CHAT_ID';
    case SEND_MSG = 'SEND_MSG';
    case DECREMENT_CHAT_ROOM_MSG_COUNT = 'DECREMENT_CHAT_ROOM_MSG_COUNT';
    case MSG_NOTIFICATION = 'MSG_NOTIFICATION';
}

enum WS_ACTIONS_NOTIFICATION: string{
    case NOTIFICATION = 'NOTIFICATION';
    case JOIN = 'JOIN';
    case CREATE_NEW_CHAT = 'CREATE_NEW_CHAT';
}

enum WS_ACTIONS_NOTIFICATION_TYPE: string{
    case NON_TYPE = 'NON_TYPE';
    case NEW_MESSAGE = 'NEW_MESSAGE';
}

enum TRIGGER_JS: string{
    case TRIGGER = 'TRIGGER_JS';
    case CREATE_NEW_CHAT = 'CREATE_NEW_CHAT';
}
