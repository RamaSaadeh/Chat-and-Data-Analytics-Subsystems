package com.example.KnowledgeProductivity.message;

import jakarta.persistence.*;

import java.security.Timestamp;
import java.time.Instant;

@Entity
@Table
public class Message {

    @Id
    @SequenceGenerator(name = "message_seq", sequenceName = "message_seq", allocationSize = 1)

    @GeneratedValue(strategy = GenerationType.SEQUENCE, generator = "message_seq")
    private Long id;
    private String content;
    private String type;
    private Long senderId;
    private Long receiverId;
    private Instant timeStamp;


    public Message() {
    }

    public Instant getTimeStamp() {
        return timeStamp;
    }

    public void setTimeStamp(Instant timeStamp) {
        this.timeStamp = timeStamp;
    }

    public Message(Long id, String content, String type, Long senderId, Long receiverId, Instant timestamp) {
        this.id = id;
        this.content = content;
        this.type = type;
        this.senderId = senderId;
        this.receiverId = receiverId;
        this.timeStamp = timestamp;
    }

    public Message(String content, String type, Long senderId, Long receiverId ,Instant timeStamp) {
        this.content = content;
        this.senderId = senderId;
        this.receiverId = receiverId;
        this.timeStamp = timeStamp;
    }


    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getContent() {
        return content;
    }

    public void setContent(String content) {
        this.content = content;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public Long getSenderId() {
        return senderId;
    }

    public void setSenderId(Long senderId) {
        this.senderId = senderId;
    }

    public Long getReceiverId() {
        return receiverId;
    }

    public void setReceiverId(Long receiverId) {
        this.receiverId = receiverId;
    }

}
