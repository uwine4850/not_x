export const ACTIONS_CHAT = {
    JOIN_CHAT_ROOM: 'JOIN_CHAT_ROOM',
    GENERATE_CHAT_ID: 'GENERATE_CHAT_ID',
    REGENERATE_CHAT_ID: 'REGENERATE_CHAT_ID',
    SEND_MSG: 'SEND_MSG',
}

export const ACTIONS_NOTIFICATION = {
    NOTIFICATION: 'NOTIFICATION',
    JOIN: 'JOIN',
}

export const ACTIONS_NOTIFICATION_TYPES = {
    NEW_MESSAGE: 'NEW_MESSAGE',
}

// join_uid - Id of the user who connected to the broadcast socket.
export const ws_notification_join = {
    action: ACTIONS_NOTIFICATION.JOIN,
    join_uid: null,
}

// recipient_id - ID of the user to whom the new message notification is sent.
// room_id - Chat Room ID.
// from_user - The ID of the user who sent the message.
// type - Notification Type.
export const ws_notification = {
    action: ACTIONS_NOTIFICATION.NOTIFICATION,
    recipient_id: null,
    room_id: null,
    from_user: null,
    type: null,
}

// room_id - Chat Room ID.
// auth_uid - The ID of the user logged into the site.
export const ws_join_chat_room = {
    action: ACTIONS_CHAT.JOIN_CHAT_ROOM,
    room_id: null,
    auth_uid: null,
}

// room_id - Chat Room ID.
// chat_user_id - Specially generated identifier to identify the user in chat.
export const ws_generate_chat_id = {
    action: ACTIONS_CHAT.GENERATE_CHAT_ID,
    room_id: null,
    chat_user_id: null,
}

// room_id - Chat Room ID.
// chat_user_id - Specially generated identifier to identify the user in chat.
export const ws_regenerate_chat_id = {
    action: ACTIONS_CHAT.REGENERATE_CHAT_ID,
    room_id: null,
    chat_user_id: null,
}

// room_id - Chat Room ID.
// interlocutor_id - The ID of the person you are chatting with. FOR SERVER RESPONSE ONLY.
// chat_user_id - Specially generated identifier to identify the user in chat.
// profile_user_id - The ID of the user logged into the site.
// msg - message text.
export const ws_send_msg = {
    action: ACTIONS_CHAT.SEND_MSG,
    room_id: null,
    interlocutor_id: null,
    chat_user_id: null,
    profile_user_id: null,
    msg: null,
}