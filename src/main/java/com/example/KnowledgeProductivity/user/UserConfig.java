package com.example.KnowledgeProductivity.user;

import com.example.KnowledgeProductivity.message.Message;
import com.example.KnowledgeProductivity.message.MessageRepository;
import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

import java.time.Instant;
import java.util.List;

@Configuration
public class UserConfig {

    @Bean
    CommandLineRunner commandLineRunner3(UserRepository userRepository) {


        return args -> {
            User mariam = new User(
                 "Mariam",
                    "$2a$12$e8djEMmjZrp2rVMx5JDPp.3QtVacqdR0bJvmh7a3dCi9OzDPpnVwK",
                    "mariam@makeitall.co.uk",
                    Role.USER

            );

            User alex = new User(
                    "Alex",
                    "$2a$12$e8djEMmjZrp2rVMx5JDPp.3QtVacqdR0bJvmh7a3dCi9OzDPpnVwK",
                    "alex@makeitall.co.uk",
                    Role.USER

            );


            User blue = new User(
                    "Blue",
                    "$2a$12$e8djEMmjZrp2rVMx5JDPp.3QtVacqdR0bJvmh7a3dCi9OzDPpnVwK",
                    "blue@makeitall.co.uk",
                    Role.USER

            );


            User red = new User(
                    "Red",
                    "$2a$12$e8djEMmjZrp2rVMx5JDPp.3QtVacqdR0bJvmh7a3dCi9OzDPpnVwK",
                    "red@makeitall.co.uk",
                    Role.USER
            );

            User green = new User(
                    "Green",
                    "$2a$12$e8djEMmjZrp2rVMx5JDPp.3QtVacqdR0bJvmh7a3dCi9OzDPpnVwK",
                    "green@makeitall.co.uk",
                    Role.USER
            );

            userRepository.saveAll(List.of(mariam,alex,blue,red,green));
        };
    }
}
