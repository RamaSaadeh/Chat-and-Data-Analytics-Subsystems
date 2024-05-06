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
    CommandLineRunner commandLineRunner(UserRepository studentRepository) {
        return args -> {
           User mariam = new User(
                    "Mariam",
                    "Snow",
                    "mariam@gmail.com",
                    LocalDate.of(2000, Month.JANUARY, 5),
                   "$2a$12$e8djEMmjZrp2rVMx5JDPp.3QtVacqdR0bJvmh7a3dCi9OzDPpnVwK"
            );

            User alex = new User(
                    "Alex",
                    "William",
                    "alex@gmail.com",
                    LocalDate.of(2003, Month.JANUARY, 5),
                    "$2a$12$e8djEMmjZrp2rVMx5JDPp.3QtVacqdR0bJvmh7a3dCi9OzDPpnVwK"
            );

            User blue = new User(
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
