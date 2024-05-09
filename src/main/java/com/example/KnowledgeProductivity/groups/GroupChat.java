package com.example.KnowledgeProductivity.groups;

import jakarta.persistence.*;

import java.time.Instant;

@Entity
@Table
public class GroupChat {

    @Id
    @SequenceGenerator(name = "group_seq", sequenceName = "group_seq", allocationSize = 1)

    @GeneratedValue(strategy = GenerationType.SEQUENCE, generator = "group_seq")
    private Long id;
    private Long adminId;
    private String groupName;

    public GroupChat() {
    }

    public GroupChat(Long id, Long adminId, String groupName) {
        this.id = id;
        this.adminId = adminId;
        this.groupName = groupName;
    }

    public GroupChat(Long adminId, String groupName) {
        this.adminId = adminId;
        this.groupName = groupName;
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public Long getAdminId() {
        return adminId;
    }

    public void setAdminId(Long adminId) {
        this.adminId = adminId;
    }

    public String getGroupName() {
        return groupName;
    }

    public void setGroupName(String groupName) {
        this.groupName = groupName;
    }
}
