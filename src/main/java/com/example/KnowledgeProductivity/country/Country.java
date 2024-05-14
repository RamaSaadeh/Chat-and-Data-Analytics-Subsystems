package com.example.KnowledgeProductivity.country;

import jakarta.persistence.*;

import java.time.Instant;

@Entity
@Table
public class Country {
    @Id
    @SequenceGenerator(name = "message_seq", sequenceName = "message_seq", allocationSize = 1)

    @GeneratedValue(strategy = GenerationType.SEQUENCE, generator = "message_seq")
    private Long id;
    private String countryName;
    private int employeeCount;

    public Country() {
    }

    public Country(Long id, String countryName, int employeeCount) {
        this.id = id;
        this.countryName = countryName;
        this.employeeCount = employeeCount;
    }

    public Country(String countryName, int employeeCount) {
        this.countryName = countryName;
        this.employeeCount = employeeCount;
    }
}
