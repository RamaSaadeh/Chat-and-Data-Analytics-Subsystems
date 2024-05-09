package com.example.KnowledgeProductivity.user;

import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

import java.time.LocalDate;
import java.time.Month;
import java.util.List;

@Configuration
public class UserConfig {

    @Bean
    CommandLineRunner commandLineRunner(CustomUserRepository studentRepository) {
        return args -> {
           CustomUser mariam = new CustomUser(
                    "Mariam",
                    "Snow",
                    "mariam@gmail.com",
                    LocalDate.of(2000, Month.JANUARY, 5),
                   "$2a$12$e8djEMmjZrp2rVMx5JDPp.3QtVacqdR0bJvmh7a3dCi9OzDPpnVwK"
            );

            CustomUser alex = new CustomUser(
                    "Alex",
                    "William",
                    "alex@gmail.com",
                    LocalDate.of(2003, Month.JANUARY, 5),
                    "$2a$12$e8djEMmjZrp2rVMx5JDPp.3QtVacqdR0bJvmh7a3dCi9OzDPpnVwK"
            );

            CustomUser blue = new CustomUser(
                    "Blue",
                    "William",
                    "blue@gmail.com",
                    LocalDate.of(2003, Month.JANUARY, 5),
                    "$2a$12$e8djEMmjZrp2rVMx5JDPp.3QtVacqdR0bJvmh7a3dCi9OzDPpnVwK"
            );

            studentRepository.saveAll(List.of(mariam,alex,blue));
        };
    }
}
