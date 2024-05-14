package com.example.KnowledgeProductivity.country;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
public class CountryController {

    private final CountryService countryService;

    public CountryController(CountryService countryService) {
        this.countryService = countryService;
    }


    @GetMapping("/dashboard/countries")
    public List<Country> getCountries() {
        return countryService.getCountryData();
    }
}
