package com.example.KnowledgeProductivity.groups;

import com.example.KnowledgeProductivity.group_user.GroupUser;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

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

    public List<GroupChat> getAllGroupDetails(List<GroupUser> listOfGroups) {
        List<GroupChat> groupDetails = new ArrayList<>();

        for (GroupUser groupUser : listOfGroups) {
            Optional<GroupChat> optionalGroupChat = groupChatRepository.findById(groupUser.getGroupId());
            optionalGroupChat.ifPresent(groupDetails::add); // Add only if present


        }


        return groupDetails;
    }

    public String getGroupDetailForGroup(GroupUser group) {
        // Use Optional to safely handle the case where no GroupChat is found
        Optional<GroupChat> groupChat = groupChatRepository.findById(group.getGroupId());

        // Return the group name if present, otherwise return a default value or null
        return groupChat.map(GroupChat::getGroupName).orElse("Default Group Name"); // Or `orElse(null)` if you prefer
    }


    public String getGroupNameById(Long groupId) {
        // Fetch the group using the repository
        Optional<GroupChat> group = groupChatRepository.findById(groupId);

        // Return the group name if the group exists
        return group.map(GroupChat::getGroupName).orElse("Group not found");
    }
}
