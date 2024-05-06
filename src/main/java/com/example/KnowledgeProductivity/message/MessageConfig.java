package com.example.KnowledgeProductivity.message;

import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

import java.time.Instant;
import java.util.List;

@Configuration
public class MessageConfig {

    @Bean
    CommandLineRunner commandLineRunner2(MessageRepository messageRepository) {


        return args -> {
            Message mariam = new Message(
                    "Lorem ipsum ",
                    "Text",
                    1L,
                    2L,
                    Instant.now()
            );

            Message alex = new Message(
                    "Impsum Lorem",
                    "Text",
                    1L,
                    2L,
                    Instant.now()
            );

            Message blue = new Message(
                    "Impsum Lorem , OMG IM DONE BLUD",
                    "Text",
                    2L,
                    1L,
                    Instant.now()
            );

            Message red = new Message(
                    "WOOOOW",
                    "Text",
                    1L,
                    3L,
                    Instant.now()
            );

            Message green = new Message(
                    "wel then",
                    "Text",
                    3L,
                    1L,
                    Instant.now()
            );

            messageRepository.saveAll(List.of(mariam,alex,blue,red,green));
        };
    }

}
