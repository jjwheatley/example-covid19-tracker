
function setAnnotations(){
    const annotations = [
        // moved recession annotation to front of array so it's behind 
        // the Macbook Air annotation
        // {
        //     note: { 
        //         title: "Background Annotation", 
        //         lineType: "none", 
        //         align: "middle",
        //         wrap: 150 //custom text wrapping
        //     },
        //     subject: {
        //         height: height - margin.top - margin.bottom,
        //         width: x(0) - x(5)
        //     },
        //     type: d3.annotationCalloutRect,
        //     y: margin.top,
        //     disable: ["connector"], // doesn't draw the connector
        //     //can pass "subject" "note" and "connector" as valid options
        //     dx: (x(new Date("2020-03-20")) - x(new Date("2020-03-22")))/2,
        //     data: { x: "2020-03-21"}
        // },
        {
            subject: {
                text: "A",
                x: "right" //badges have an x of "left" or "right"
            },
            data: { x: 10, y: 80}
        },
        {
            subject: { 
                text: "B" 
            },
            data: { x: 20, y: 80}
        },
        {
            subject: {
                text: "C",
                y: "bottom" //badges have a y of "top" or "bottom"
            },
            data: { x: 30, y: 80}
        },
        {
            subject: {
                text: "D",
                y: "bottom",
                x: "right"
            },
            data: { x: 40, y: 80}
        }
    ]

    const type = d3.annotationCustomType(
            d3.annotationBadge, 
            {"subject":{"radius": 10 }}
        )

        const makeAnnotations = d3.annotation()
            .type(type)
            .accessors({ 
            x: function(d){ return x(new Date(d.x))},
            y: function(d){ return y(d.y) }
            })
            .annotations(annotations)

        d3.select("svg")
            .append("g")
            .attr("class", "annotation-group")
            .call(makeAnnotations)


        //annotations for legend
        const annotationsLegend = [{
            note: { label: "Annotation A" },
            subject: { text: "A" }
            },
            {
            note: { label: "Annotation B" },
            subject: { text: "B" }
            },
            {
            note: { label: "Annotation C" },
            subject: { text: "C" }
            },
            {
            note: { label: "Annotation D" },
            subject: { text: "D" }
            }
            ].map(function(d, i){
            d.x = margin.left + i*200
            d.y = 415 
            d.subject.x = "right" 
            d.subject.y = "bottom" 
            d.subject.radius = 10
            return d
            })

        const makeLegendAnnotations = d3.annotation()
            .type(d3.annotationBadge)
            .annotations(annotationsLegend)

        d3.select("svg")
            .append("g")
            .attr("class", "annotation-legend")
            .call(makeLegendAnnotations)
            
        d3.select("svg g.annotation-legend")
            .selectAll('text.legend')
            .data(annotationsLegend)
            .enter()
            .append('text')
            .attr('class', 'legend')
            .text(function(d){ return d.note.label })
            .attr('x', function(d, i){ return margin.left + 30 + i*200 })
            .attr('y', 430)

}