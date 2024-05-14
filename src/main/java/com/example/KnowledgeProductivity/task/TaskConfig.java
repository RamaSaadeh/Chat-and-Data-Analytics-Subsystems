package com.example.KnowledgeProductivity.task;

import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

import java.util.List;

@Configuration
public class TaskConfig {

    @Bean
    CommandLineRunner commandLineRunner6(TaskRepository taskRepository) {
        return args -> {
            Task task1 = new Task(
                    1L, // Project ID
                    1L, // Assigned Employee ID
                    3L,
                    "In Progress",
                    "Build the main dashboard interface"
            );

            Task task2 = new Task(
                    1L, // Project ID
                    2L, // Assigned Employee ID
                    2L,
                    "Completed",
                    "Install and configure the PostgreSQL database"
            );

            Task task3 = new Task(
                    2L, // Project ID
                    3L, // Assigned Employee ID
                    1L,
                    "Not Started",
                    "Outline the marketing activities for the product launch"
            );

            taskRepository.saveAll(List.of(task1, task2, task3));
        };
    }
}