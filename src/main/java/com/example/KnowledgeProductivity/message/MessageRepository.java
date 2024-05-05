package com.example.KnowledgeProductivity.message;

import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

//This Interface/Repository is used to generate sql statement equivalent

@Repository
public interface MessageRepository extends JpaRepository<Message, Long> {

    // This just makes a sql query as follows SELECT * FROM message WHERE receiver_id = receiverId
    List<Message> findAllByReceiverId(Long receiverId);
}
