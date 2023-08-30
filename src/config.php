<?php
namespace config;
const PATH_TO_MEDIA_USERS = '/var/www/html/media/users/';
const MAX_IMAGES = 2;

enum WS_ACTIONS_CHAT: string{
    case JOIN_CHAT_ROOM = 'JOIN_CHAT_ROOM';
    case GENERATE_CHAT_ID = 'GENERATE_CHAT_ID';
    case REGENERATE_CHAT_ID = 'REGENERATE_CHAT_ID';
    case SEND_MSG = 'SEND_MSG';
}

enum WS_ACTIONS_NOTIFICATION: string{
    case NOTIFICATION = 'NOTIFICATION';
    case JOIN = 'JOIN';
}

enum WS_ACTIONS_NOTIFICATION_TYPE: string{
    case NON_TYPE = 'NON_TYPE';
    case NEW_MESSAGE = 'NEW_MESSAGE';
}
