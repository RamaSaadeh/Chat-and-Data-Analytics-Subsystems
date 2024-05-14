package com.example.KnowledgeProductivity.project;

import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

import java.time.LocalDate;
import java.util.List;

@Configuration
public class ProjectConfig {

    @Bean
    CommandLineRunner commandLineRunner5(ProjectRepository projectRepository) {
        return args -> {
            Project project1 = new Project(
                    "Project Apollo",
                    "In Progress",
                    LocalDate.parse("2023-01-01"),
                    LocalDate.parse("2023-12-31")
            );

            Project project2 = new Project(
                    "Project Gemini",
                    "Completed",
                    LocalDate.parse("2022-01-01"),
                    LocalDate.parse("2022-12-31")
            );

            Project project3 = new Project(
                    "Project Mercury",
                    "Not Started",
                    LocalDate.parse("2024-01-01"),
                    LocalDate.parse("2024-12-31")
            );

            Project project4 = new Project(
                    "Project Sun",
                    "Overdue",
                    LocalDate.parse("2024-01-01"),
                    LocalDate.parse("2024-12-31")
            );

            Project project5 = new Project(
                    "Project moon",
                    "Overdue",
                    LocalDate.parse("2024-01-01"),
                    LocalDate.parse("2024-12-31")
            );

            projectRepository.saveAll(List.of(project1, project2, project3,project4,project5));
        };
    }
}
