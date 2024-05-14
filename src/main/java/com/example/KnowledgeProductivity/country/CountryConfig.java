package com.example.KnowledgeProductivity.country;

import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

import java.util.List;

@Configuration
public class CountryConfig {

    @Bean
    CommandLineRunner commandLineRunner7(CountryRepository countryRepository) {
        return args -> {
            Country country1 = new Country(
                    "United States",
                    580
            );

            Country country2 = new Country(
                    "Canada",
                    430
            );

            Country country3 = new Country(
                    "United Kingdom",
                    448
            );

            Country country4 = new Country(
                    "Germany",
                    1380
            );

            Country country5 = new Country(
                    "China",
                    1200
            );

            countryRepository.saveAll(List.of(country1, country2, country3, country4, country5));
        };
    }
}

