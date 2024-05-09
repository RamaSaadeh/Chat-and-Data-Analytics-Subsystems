package com.example.KnowledgeProductivity.groups;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

@Service
public class GroupChatService {

    private final GroupChatRepository groupChatRepository;

    @Autowired
    public GroupChatService(GroupChatRepository groupChatRepository) {
        this.groupChatRepository = groupChatRepository;
    }

    public Long getJustCreateGroupChatId() {

        return 1L;
    }

    public void createNewGroupChat(GroupChat groupChat) {
         groupChatRepository.save(groupChat);
    }
}
